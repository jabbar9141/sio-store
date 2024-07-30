<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Location;
use App\Models\product\ProductModel;
use App\Models\ProductVariation;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopOrderPayment;
use App\Models\User;
use App\MyHelpers;
use App\Notifications\OrderStatusUpdated;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class ShopOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = ShopOrder::where('user_id', Auth::id())
            ->orderBy('created_at', 'DESC')->paginate(20);

        return view('backend.profile.orders.index', ['orders' => $orders]);
    }

    public function initialize(Request $request)
    {

        $cart = session('cart');
        if (($cart) && count($cart) > 0) {
            try {
                $old_order = ShopOrder::where('user_id', Auth::id())
                    ->where('metadata', $cart)
                    ->where('status', 'Pending')
                    ->orderBy('id', 'DESC')
                    ->first();
                if ($old_order) {
                    $order = $old_order;
                } else {
                    $order = new ShopOrder;
                    $order->order_id = UuidV4::uuid4();
                    $order->user_id = Auth::id();
                    $order->metadata = json_encode($cart);
                    $order->save();

                    foreach ($cart as $i) {
                        $t = new ShopOrderItem;
                        $t->order_id = $order->id;
                        $t->item_id = $i['product_id'];
                        $t->variant = $i['variations'] ?? '';
                        $t->price = $i['price'] / $i['qty'];
                        $t->qty = $i['qty'] ?? 1;
                        $t->total_price = $i['price'];
                        $t->product_variation_id = $i['variation_id'] ?? null;
                        $t->save();
                    }
                }
                $addr = Address::where('user_id', Auth::id())->where('status', 1)->paginate(10);

                return view('user.init_order', compact('order', 'addr'));
            } catch (\Exception $e) {
                Log::error($e->getMessage(), [$e]);
                return back()->with(['error' => "Failed to initialize order, please try again later."]);
            }
        } else {
            return back()->with(['error' => "Your cart is empty"]);
        }
    }
    /**
     * estimate the shippng cost of a shipping order
     */

    public function estimate_order_ship_cost(Request $request)
    {
        $request->validate([
            'address_id' => 'required',
            'order_id' => 'required'
        ]);

        try {
            $destination = Address::find($request->get('address_id'));
            $o = ShopOrder::where('id', $request->get('order_id'))->first();

            $items = $o->items;
            $shipping_costs = []; // Initialize array to store shipping costs for each provider

            foreach ($items as $item) {
                $product = ProductModel::where('product_id', $item->item_id)->first();
                if (null != $product->ships_from) {
                    $origin = Location::find($product->ships_from);
                    $shipping_data = [
                        "width" => $product->width,
                        "height" => $product->height,
                        "weight" => $product->weight,
                        "length" => $product->length,
                        "count" => $item->qty,
                        "item_desc" => substr($product->product_name, 0, 24),
                        "item_value" => $item->price ?? $item->variation->price, // changed ($item->price ?? $product->product_price,) to ($item->price ?? $item->variation->price,)
                        "origin_city" => $origin->name,
                        "dest_city" => $destination->city,
                        "origin_zip" => $origin->zip,
                        "dest_zip" => $destination->zip,
                        "origin_country" => $origin->country_code,
                        "dest_country" => $destination->country
                    ];

                    $shipping_cost = ProductController::estimate_shipping($shipping_data);
                    $shipping_cost = ((array) json_decode($shipping_cost));

                    // Accumulate shipping costs for each provider
                    foreach ($shipping_cost as $provider => $cost) {
                        if (isset($shipping_costs[$provider])) {
                            $shipping_costs[$provider] += (($cost != '') ? $cost : 0);
                        } else {
                            $shipping_costs[$provider] = (($cost != '') ? $cost : 0);
                        }
                    }
                }
            }
            //save the estimation in the db
            $o->shipping_cost = $shipping_costs;
            $o->save();

            $markup = '';
            if (count($shipping_costs) > 0) {
                foreach ($shipping_costs as $provider => $cost) {
                    if ($cost > 0) {
                        $provider_upper = strtoupper($provider);
                        $markup .= '
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="shipping_provider" id="' . $provider . '" onchange="calculate_total(this)"
                                    value="' . $provider_upper . '" data-cost="' . $cost . '" required>
                                <label class="form-check-label" for="' . $provider . '">' . $provider_upper . ' - ' . MyHelpers::fromEuro(Auth::user()->currency_id, $cost) . '</label>
                            </div>
                        ';
                    }
                }
            }

            // Output the cumulative shipping costs for each provider
            return response()->json([
                'shipping_costs' => $shipping_costs,
                'markup' => $markup,
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['err' => 'an error occurred while calculating shipping fees for the order'], 500);
        }
    }

    public function submit(Request $request, $order_id)
    {
        // dd($request->all());
        $request->validate([
            'billing_address' => 'required',
            'shipping_provider' => 'required',
            'payment' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $order = ShopOrder::where('id', $order_id)->first();

            $order->billing_address_id = $request->billing_address;
            $order->shipping_address_id = $request->billing_address;
            $order->shipping_method = $request->shipping_provider;
            $order->payment_method = $request->payment;
            $order->save();

            $order->shipping_cost = (array) json_decode($order->shipping_cost);
            $YOUR_DOMAIN = env('APP_URL');

            // dd($order->items);

            if ($request->payment == "STRIPE") {
                $items = [];

                foreach ($order->items as $it) {
                    $price = $it->total_price ?? 0; //
                    if ($price == 0) {
                        $price = $it->variation->price * $it->qty;
                    }
                    $t = [
                        'price_data' => [
                            'currency' => 'EUR',
                            'product_data' => [
                                'name' => $it->item->product_name,
                            ],
                            'unit_amount_decimal' => ($price) * 100
                        ],
                        'quantity' => $it->qty,
                    ];
                    array_push($items, $t);
                }

                //one last item to represent the shipping costs
                $t = [
                    'price_data' => [
                        'currency' => 'EUR',
                        'product_data' => [
                            'name' => 'Shipping fee for ' . $request->shipping_provider,
                        ],
                        'unit_amount_decimal' => ($order->shipping_cost[strtolower($request->shipping_provider)]) * 100
                    ],
                    'quantity' => 1,
                ];

                array_push($items, $t);

                \Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));
                header('Content-Type: application/json');



                $checkout_session = \Stripe\Checkout\Session::create([
                    'line_items' => $items,
                    'mode' => 'payment',
                    'success_url' => $YOUR_DOMAIN . '/payment-success?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => $YOUR_DOMAIN . '/payment-error?session_id={CHECKOUT_SESSION_ID}',
                ]);

                //create our own order payment intent
                $lp = new ShopOrderPayment;

                $lp->ref = $checkout_session->id;
                $lp->payment_method = 'STRIPE';
                $lp->order_id = $order->id;
                $lp->amount = $checkout_session->amount_total;
                $lp->metadata = json_encode($checkout_session);
                $lp->status = 'Pending';
                $lp->save();

                //empty cart
                $cart = Cart::where('user_id', Auth::id())->where('status', 1)->first();
                $cart->status = 0;
                $cart->save();
                session()->forget('cart');

                DB::commit();
                //send them to stripe hosted checout page
                return redirect()->away($checkout_session->url);
            } elseif ($request->payment == "SUMUP") {
                $cost = 0;

                foreach ($order->items as $it) {
                    $price = $it->total_price ?? 0; //
                    if ($price == 0) {
                        $price = $it->variation->price * $it->qty;
                    }
                    $cost = $cost + ($price * 100);
                }

                //one last item to represent the shipping costs
                $cost = $cost + ($order->shipping_cost[strtolower($request->shipping_provider)] * 100);

                try {
                    $sumup = new \SumUp\SumUp([
                        'app_id'     => env('SUMUP_KEY'),
                        'app_secret' => env('SUMUP_SECRET'),
                        'grant_type' => 'client_credentials',
                        'scopes'      => ['payments', 'transactions.history', 'user.app-settings', 'user.profile_readonly']
                    ]);
                    // $accessToken = $sumup->getAccessToken();
                    // $value = $accessToken->getValue();
                    $checkoutService = $sumup->getCheckoutService();
                    $checkoutResponse = $checkoutService->create(
                        $cost / 100,
                        'EUR',
                        $order_id . "_" . rand(100000, 999999),
                        env('SUMUP_EMAIL'),
                        'Payment Intent for order: ' . $order_id,
                        Auth::user()->email,
                        $YOUR_DOMAIN . '/payment-success?session_id={CHECKOUT_SESSION_ID}'
                    );
                    $paymentIntent = $checkoutResponse->getBody();
                } catch (\SumUp\Exceptions\SumUpAuthenticationException $e) {
                    Log::error('Authentication error: ' . $e->getMessage(), [$e]);
                    return back()->with(['error' => "SumUp checkout failed, please try again later."]);
                } catch (\SumUp\Exceptions\SumUpResponseException $e) {
                    Log::error('Response error: ' . $e->getMessage(), [$e]);
                    return back()->with(['error' => "SumUp checkout failed, please try again later."]);
                } catch (\SumUp\Exceptions\SumUpSDKException $e) {
                    Log::error('SumUp SDK error: ' . $e->getMessage(), [$e]);
                    return back()->with(['error' => "SumUp checkout failed, please try again later."]);
                }

                //create our own order payment intent
                $lp = new ShopOrderPayment;

                $lp->ref = $paymentIntent->id;
                $lp->payment_method = 'SUMUP';
                $lp->order_id = $order->id;
                $lp->amount = $cost;
                $lp->metadata = json_encode($paymentIntent);
                $lp->status = 'Pending';
                $lp->save();

                foreach ($order->items as $item) {
                    $variation = ProductVariation::where('id', $item->product_variation_id)->first();
                    if ($variation) {
                        $variation->product_quantity = $variation->product_quantity - $item->qty;
                        $variation->save();
                    } else {
                        $item->item->product_quantity = $item->item->product_quantity - $item->qty;
                        $item->item->save();
                    }
                }

                //empty cart
                $cart = Cart::where('user_id', Auth::id())->where('status', 1)->first();
                $cart->status = 0;
                $cart->save();
                session()->forget('cart');

                DB::commit();
                //return the cient secret of the payment intent
                return view('user.complete_order', ['client_secret' => $paymentIntent->id]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), [$e]);
            return back()->with(['error' => "Failed to initiate payment, please try again later"]);
        }
    }

    public function payment_success(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        try {
            //get stripe details
            $session = $stripe->checkout->sessions->retrieve($request->get('session_id'));


            //update the order payment status
            $t = ShopOrderPayment::where('ref', $request->get('session_id'))->first();
            $t->status = 'Done';
            $t->save();

            //update the order
            $u = ShopOrder::where('id', $t->order_id)->first();
            $u->status = 'Completed';
            $u->save();

            foreach ($u->items as $item) {
                $variation = ProductVariation::where('id', $item->product_variation_id)->first();
                if ($variation) {
                    $variation->product_quantity = $variation->product_quantity - $item->qty;
                    $variation->save();
                } else {
                    $item->item->product_quantity = $item->item->product_quantity - $item->qty;
                    $item->item->save();
                }
            }


            return view('user.stripe', ['status' => 'success', 'session' => $session]);
        } catch (\Error $e) {
            Log::error($e->getMessage(), [$e]);
            //TODO:implement what is shown when it fails to retrive stripe data
        }
    }

    public function payment_error(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        try {
            //get stripe details
            $session = $stripe->checkout->sessions->retrieve($request->get('session_id'));


            //update the order status
            $t = ShopOrderPayment::where('ref', $request->get('session_id'))->first();
            $t->status = 'Done';
            $t->save();

            return view('user.stripe', ['status' => 'error', 'session' => $session]);
        } catch (\Error $e) {
            Log::error($e->getMessage(), [$e]);
            //TODO:implement what is shown when it fails to retrive stripe data
        }
    }

    public function updateItemStatus(Request $request, $item_id)
    {
        try {
            $request->validate([
                'order_status' => 'required'
            ]);

            DB::beginTransaction();

            $item = ShopOrderItem::where('id', $item_id)->first();
            $item->status = $request->order_status;
            $item->save();

            $rec1 = User::find(Auth::id());
            $rec2 = User::find($item->order->user_id);

            //if the status is completed, we check if it is the last to be complted among the order and update the order to completed
            if ($request->order_status == 'Completed') {

                $t = ShopOrderItem::where('order_id', $item->order_id)->where('status', '!=', 'Completed')->count();

                if ($t <= 1) {
                    $order = ShopOrder::where('id', $item->order_id)->first();
                    $order->status = 'Completed';
                    $order->save();
                }
            }

            Notification::send([$rec1, $rec2], new OrderStatusUpdated($item));

            DB::commit();
            return back()->with(['success' => 'Status Updated']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), [$e]);
            return back()->with(['error' => "Failed to initiate payment, please try again later"]);
        }
    }

    public function createItemShippingLabel(Request $request, $item_id)
    {
        $request->validate([
            'pickup_date' => 'required'
        ]);
        $item = ShopOrderItem::where('id', $item_id)->first();
        $product = $item->item;
        $dest_address = Address::where('id', $item->order->shipping_address_id)->first();
        $origin = Location::where('id', $item->item->ships_from)->first();
        $origin_vendor = Auth::user();
        $origin_vendor_shop = Auth::user()->vendor_shop;

        $response = Http::withHeaders(['Accept' => 'application/json'])->post(env('SHIPPING_POST_API_URL'), [
            'width' => $product->width,
            'height' => $product->height,
            'weight' => $product->weight,
            'length' => $product->length,
            'count' => $item->qty,
            'item_desc' => $product->product_name,
            'item_value' => $product->product_price,
            'origin_country' => $origin->country_code,
            'origin_zip' => $origin->zip,
            'origin_city' => $origin->name,
            'origin_name' => $origin_vendor->name . '(' . $origin_vendor_shop->shop_name . ')',
            'origin_address' => $origin_vendor->address,
            'origin_email' => $origin_vendor->email,
            'pickup_date' => $request->pickup_date,
            'dest_city' => $dest_address->city,
            'dest_zip' => $dest_address->zip,
            'dest_email' => $dest_address->email,
            'dest_phone' => $dest_address->phone,
            'dest_address' => $dest_address->address1,
            'dest_address2' => $dest_address->address2,
            'dest_name' => $dest_address->firstname . ' ' . $dest_address->lastname,
            'dest_country' => $dest_address->country,
            'dest_state' => $dest_address->state,
            'ref' => $item->order->order_id . '_' . $item->id,
        ]);

        if ($response->successful()) {
            $body = $response->json();
            $item->tracking_id = $body['data']['tracking_id'];
            $item->status = 'Shipped';
            $item->save();

            return back()->with(['success' => $body['message']]);
        } else {
            Log::error($response->json()['message'], [$response->json()]);
            return back()->with(['error' => $response->json()['message']]);
        }
    }


    public function createPayment()
    {

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->setCurrency('EUR');
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('success.payment'),
                "cancel_url" => route('cancel.payment'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" => "12.00"
                    ]
                ]
            ]
        ]);
        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
            return $response;
            return redirect()
                ->route('payment.mathod.view')
                ->with('msg', 'Something went wrong');
        } else {
            return $response;
            return redirect()
                ->route('payment.mathod.view')
                ->with('msg', $response['message'] ?? 'Something went wrong');
        }
    }

    public function success(Request $request)
    {

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            return $response;
            return redirect('/')
                ->with('success1', 'Success');
        } else {
            return $response;
            return redirect('/')
                ->with('msg', $response['message'] ?? 'Something went wrong');
        }
    }

    public function cancel()
    {
        return $response ?? 'Operación cancelada';
        return redirect('/')
            ->with('msg', $response['message'] ?? 'Operación cancelada');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShopOrder  $shopOrder
     * @return \Illuminate\Http\Response
     */
    public function show(ShopOrder $shopOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShopOrder  $shopOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(ShopOrder $shopOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShopOrder  $shopOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShopOrder $shopOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShopOrder  $shopOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShopOrder $shopOrder)
    {
        //
    }
}
