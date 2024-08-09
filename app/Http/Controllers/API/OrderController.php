<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Location;
use App\Models\product\ProductModel;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopOrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class OrderController extends Controller
{

    public function show($order_id)
    {

        $order = ShopOrder::where('user_id', Auth::guard('api')->id())
            ->where('id', $order_id)->first();

        //decode the items and attach more details
        if (!empty($order)) {
            $newMeta = [];
            foreach (json_decode($order->metadata) as $item) {
                $p = ProductModel::find($item->product_id);
                $item->product_data = $p;
                array_push($newMeta,  $item);
            }

            $order->metadata = $newMeta;
        }
        // Return JSON response
        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }

    public function index(Request $request)
    {
        // Define pagination parameters
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $orders = ShopOrder::where('user_id', Auth::guard('api')->id())
            ->orderBy('created_at', 'DESC')->paginate($perPage);

        $newItems = [];
        if (!empty($orders->items())) {
            foreach ($orders->items() as $order_item) {
                $newMeta = [];
                foreach (json_decode($order_item->metadata) as $item) {
                    $p = ProductModel::find($item->product_id);
                    $item->product_data = $p;
                    array_push($newMeta,  [$item]);
                }

                $order_item->metadata = $newMeta;

                array_push($newItems, $order_item);
            }
        }
        // Return paginated JSON response
        return response()->json([
            'data' => $newItems,
            'current_page' => $orders->currentPage(),
            'per_page' => $orders->perPage(),
            'total' => $orders->total(),
            'last_page' => $orders->lastPage(),
            'next_page_url' => $orders->nextPageUrl(),
            'previous_page_url' => $orders->previousPageUrl()
        ]);
    }


    public function initialize(Request $request)
    {
        $request->validate([
            'address_id' => 'required'
        ]);
        $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();

        if ($cart) {
            try {
                DB::beginTransaction();
                $old_order = ShopOrder::with(['items.item'])->where('user_id', Auth::guard('api')->id())
                    ->where('metadata', $cart->metadata)
                    ->where('status', 'Pending')
                    ->orderBy('id', 'DESC')
                    ->first();
                if ($old_order) {
                    $order = $old_order;
                } else {
                    $order = new ShopOrder;
                    $order->order_id = UuidV4::uuid4();
                    $order->user_id = Auth::guard('api')->id();
                    $order->metadata = $cart->metadata;
                    $order->save();

                    foreach (json_decode($cart->metadata) as $i) {
                        $t = new ShopOrderItem;
                        $t->order_id = $order->id;
                        $t->item_id = $i->product_id;
                        $t->variant = $i->variant ?? '';
                        $t->price = $i->price;
                        $t->qty = $i->qty ?? 1;
                        $t->save();
                    }
                }
                $shipping_cost = $this->estimate_order_ship_cost($request->address_id, $order->id);
                DB::commit();
                $order = ShopOrder::with(['items.item'])->where('id', $order->id)->first();
                return response()->json(['message' => 'Order Initialized', 'order' => $order, 'shipping_cost' => $shipping_cost]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage(), [$e]);
                return response()->json(['error' => "Failed to initialize order, please try again later."], 500);
            }
        } else {
            return response()->json(['error' => "Your cart is empty"], 400);
        }
    }
    /**
     * estimate the shippng cost of a shipping order
     */

    public function estimate_order_ship_cost($address_id, $order_id)
    {

        try {

            $destination = Address::find($address_id);
            $o = ShopOrder::where('id', $order_id)->first();

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
                        "item_value" => $product->product_price,
                        "origin_city" => $origin->name,
                        "dest_city" => $destination->city,
                        "origin_zip" => $origin->zip,
                        "dest_zip" => $destination->zip,
                        "origin_country" => $origin->country_code,
                        "dest_country" => $destination->country
                    ];

                    $shipping_cost = \App\Http\Controllers\ProductController::estimate_shipping($shipping_data);
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
            $o->billing_address_id = $address_id;
            $o->shipping_address_id = $address_id;
            $o->save();

            // Output the cumulative shipping costs for each provider
            return $shipping_costs;
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return false;
        }
    }

    public function submit(Request $request, $order_id)
    {
        // dd($request->all());
        $request->validate([
            'shipping_provider' => 'required',
            'payment_provider' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $order = ShopOrder::where('id', $order_id)->first();

            $order->shipping_method = $request->shipping_provider;
            $order->payment_method = $request->payment_provider;
            $order->save();

            $order->shipping_cost = (array) json_decode($order->shipping_cost);

            // dd($order->items);

            $cost = 0;

            foreach ($order->items as $it) {
                $cost = $cost + ($it->item->product_price * 100);
            }

            //one last item to represent the shipping costs
            $cost = $cost + ($order->shipping_cost[strtolower($request->shipping_provider)] * 100);



            if ($request->payment_provider == "STRIPE") {

                \Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));

                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $cost,
                    'currency' => 'eur',
                    'automatic_payment_methods' => ['enabled' => true],
                    'metadata' => [
                        'order_id' => $order_id,
                    ]
                ]);

                //create our own order payment intent
                $lp = new ShopOrderPayment;

                $lp->ref = $paymentIntent->id;
                $lp->payment_method = 'STRIPE';
                $lp->order_id = $order->id;
                $lp->amount = $paymentIntent->amount;
                $lp->metadata = json_encode($paymentIntent);
                $lp->status = 'Pending';
                $lp->save();

                //empty cart
                $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
                $cart->status = 0;
                $cart->save();

                DB::commit();
                //return the cient secret of the payment intent
                return response()->json(['status' => true, 'message' => 'Payment intent created successfully', 'data' => ['client_secret' => $paymentIntent->client_secret, 'method' => 'STRIPE']]);
            } elseif ($request->payment_provider == "SUMUP") {

                try {
                    $sumup = new \SumUp\SumUp([
                        'app_id'     => env('SUMUP_KEY'),
                        'app_secret' => env('SUMUP_SECRET'),
                        'grant_type' => 'client_credentials',
                        'scopes'      => ['payments', 'transactions.history', 'user.app-settings', 'user.profile_readonly']
                    ]);
                    $accessToken = $sumup->getAccessToken();
                    $value = $accessToken->getValue();
                    $checkoutService = $sumup->getCheckoutService();
                    $checkoutResponse = $checkoutService->create(
                        ($cost / 100),
                        'EUR',
                        $order_id . "_" . rand(100000, 999999),
                        env('SUMUP_EMAIL'),
                        'Payment Intent for order: ' . $order_id,
                        Auth::user()->email,
                        route('payment_success_sumup')
                    );
                    $paymentIntent = $checkoutResponse->getBody();
                    //  pass the $chekoutId to the front-end to be processed
                } catch (\SumUp\Exceptions\SumUpAuthenticationException $e) {
                    Log::error('Authentication error: ' . $e->getMessage(), [$e]);
                    return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
                } catch (\SumUp\Exceptions\SumUpResponseException $e) {
                    Log::error('Response error: ' . $e->getMessage(), [$e]);
                    return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
                } catch (\SumUp\Exceptions\SumUpSDKException $e) {
                    Log::error('SumUp SDK error: ' . $e->getMessage(), [$e]);
                    return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
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

                DB::commit();
                //return the cient secret of the payment intent
                return view('user.complete_order_mobile', ['client_secret' => $paymentIntent->id, 'orderId' => $order->id]);
                // return response()->json(['status' => true, 'message' => 'Payment intent created successfully', 'data' => ['client_secret' => $paymentIntent->id, 'method' => "SUMUP"]]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => "Failed to initiate payment, please try again later"], 500);
        }
    }

    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                env("STRIPE_SECRET")
            );
        } catch (SignatureVerificationException $e) {
            return response()->json(['status' => false, 'message' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $intent = $event->data->object;
                $paymentIntent = $intent->id;

                $transaction = ShopOrderPayment::where('ref', $paymentIntent)->first();

                if ($transaction) {
                    $transaction->status = 'Done';
                    $transaction->save();
                }

                //empty cart
                $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
                $cart->status = 0;
                $cart->save();

                break;
            case 'payment_intent.payment_failed':
                $intent = $event->data->object;
                $paymentIntent = $intent->id;

                $transaction = ShopOrderPayment::where('ref', $paymentIntent)->first();

                if ($transaction) {
                    $transaction->status = 'Cancelled';
                    $transaction->save();
                }
                break;
        }

        return response()->json(['status' => true, 'message' => 'success']);
    }

    public function handleSumUpWebhook(Request $request)
    {
        //obtain the ID of the related event/checkout
        $id = $request->id;

        //verify the checkout current status
        try {
            $sumup = new \SumUp\SumUp([
                'app_id'     => env('SUMUP_KEY'),
                'app_secret' => env('SUMUP_SECRET'),
                'grant_type' => 'client_credentials',
                'scopes'      => ['payments']
            ]);
            // $accessToken = $sumup->getAccessToken();
            // $value = $accessToken->getValue();
            $checkoutService = $sumup->getCheckoutService();

            $tx_status = $checkoutService->findById($id)->getBody();
        } catch (\SumUp\Exceptions\SumUpAuthenticationException $e) {
            Log::error('Authentication error: ' . $e->getMessage(), [$e]);
            return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
        } catch (\SumUp\Exceptions\SumUpResponseException $e) {
            Log::error('Response error: ' . $e->getMessage(), [$e]);
            return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
        } catch (\SumUp\Exceptions\SumUpSDKException $e) {
            Log::error('SumUp SDK error: ' . $e->getMessage(), [$e]);
            return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
        }

        switch ($tx_status->status) {
            case 'PAID':
                $transaction = ShopOrderPayment::where('ref', $id)->first();

                if ($transaction) {
                    $transaction->status = 'Done';
                    $transaction->save();
                    $order = ShopOrder::where('id', $transaction->order_id)->first();
                    $order->status = 'Completed';
                    $order->save();
                }

                //empty cart
                $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
                $cart->status = 0;
                $cart->save();

                break;
            case 'PENDING':
                $transaction = ShopOrderPayment::where('ref', $id)->first();

                if ($transaction) {
                    $transaction->status = 'Pending';
                    $transaction->save();
                    $order = ShopOrder::where('id', $transaction->order_id)->first();
                        $order->status = 'Pending';
                        $order->save();
                }

                break;
            case 'FAILED':

                $transaction = ShopOrderPayment::where('ref', $id)->first();
                if ($transaction) {
                    $transaction->status = 'Cancelled';
                    $transaction->save();
                    $order = ShopOrder::where('id', $transaction->order_id)->first();
                    $order->status = 'Cancelled';
                    $order->save();
                }

                //empty cart
                $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
                $cart->status = 0;
                $cart->save();
                break;
        }

        return response()->json(['status' => true, 'message' => 'success']);
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
            $u = ShopOrder::where('id', $t->order_id)->first();
            $u->status = 'Pending';
            $u->save();

            return view('user.stripe', ['status' => 'error', 'session' => $session]);
        } catch (\Error $e) {
            Log::error($e->getMessage(), [$e]);
            //TODO:implement what is shown when it fails to retrive stripe data
        }
    }

    public function payment_complete_sumup(Request $request, $id)
    {
        try {
            //verify the checkout current status
            try {
                $sumup = new \SumUp\SumUp([
                    'app_id'     => env('SUMUP_KEY'),
                    'app_secret' => env('SUMUP_SECRET'),
                    'grant_type' => 'client_credentials',
                    'scopes'      => ['payments']
                ]);
                // $accessToken = $sumup->getAccessToken();
                // $value = $accessToken->getValue();
                $checkoutService = $sumup->getCheckoutService();

                $tx_status = $checkoutService->findById($id)->getBody();
            } catch (\SumUp\Exceptions\SumUpAuthenticationException $e) {
                Log::error('Authentication error: ' . $e->getMessage(), [$e]);
                return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
            } catch (\SumUp\Exceptions\SumUpResponseException $e) {
                Log::error('Response error: ' . $e->getMessage(), [$e]);
                return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
            } catch (\SumUp\Exceptions\SumUpSDKException $e) {
                Log::error('SumUp SDK error: ' . $e->getMessage(), [$e]);
                return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
            }

            switch ($tx_status->status) {
                case 'PAID':
                    $transaction = ShopOrderPayment::where('ref', $id)->first();

                    if ($transaction) {
                        $transaction->status = 'Done';
                        $transaction->save();
                        $order = ShopOrder::where('id', $transaction->order_id)->first();
                        $order->status = 'Completed';
                        $order->save();
                    }

                    //empty cart
                    $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
                    $cart->status = 0;
                    $cart->save();

                    break;
                case 'PENDING':
                    $transaction = ShopOrderPayment::where('ref', $id)->first();

                    if ($transaction) {
                        $transaction->status = 'Pending';
                        $transaction->save();
                        $order = ShopOrder::where('id', $transaction->order_id)->first();
                        $order->status = 'Pending';
                        $order->save();
                    }

                    break;
                case 'FAILED':

                    $transaction = ShopOrderPayment::where('ref', $id)->first();
                    if ($transaction) {
                        $transaction->status = 'Cancelled';
                        $transaction->save();
                        $order = ShopOrder::where('id', $transaction->order_id)->first();
                        $order->status = 'Cancelled';
                        $order->save();
                    }

                    //empty cart
                    $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
                    $cart->status = 0;
                    $cart->save();
                    break;
            }

            return view('user.sumup', ['session' => $tx_status]);
        } catch (\Error $e) {
            Log::error($e->getMessage(), [$e]);
            //TODO:implement what is shown when it fails to retrive stripe data
        }
    }

    public function payment_complete_sumup_mobile(Request $request, $id)
    {
        try {
            //verify the checkout current status
            try {
                $sumup = new \SumUp\SumUp([
                    'app_id'     => env('SUMUP_KEY'),
                    'app_secret' => env('SUMUP_SECRET'),
                    'grant_type' => 'client_credentials',
                    'scopes'      => ['payments']
                ]);
                // $accessToken = $sumup->getAccessToken();
                // $value = $accessToken->getValue();
                $checkoutService = $sumup->getCheckoutService();

                $tx_status = $checkoutService->findById($id)->getBody();
            } catch (\SumUp\Exceptions\SumUpAuthenticationException $e) {
                Log::error('Authentication error: ' . $e->getMessage(), [$e]);
                return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
            } catch (\SumUp\Exceptions\SumUpResponseException $e) {
                Log::error('Response error: ' . $e->getMessage(), [$e]);
                return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
            } catch (\SumUp\Exceptions\SumUpSDKException $e) {
                Log::error('SumUp SDK error: ' . $e->getMessage(), [$e]);
                return response()->json(['status' => false, 'message' => "A payment error occured"], 500);
            }

            switch ($tx_status->status) {
                case 'PAID':
                    $transaction = ShopOrderPayment::where('ref', $id)->first();

                    if ($transaction) {
                        $transaction->status = 'Done';
                        $transaction->save();
                        $order = ShopOrder::where('id', $transaction->order_id)->first();
                        $order->status = 'Completed';
                        $order->save();
                    }

                    //empty cart
                    $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
                    $cart->status = 0;
                    $cart->save();

                    break;
                case 'PENDING':
                    $transaction = ShopOrderPayment::where('ref', $id)->first();

                    if ($transaction) {
                        $transaction->status = 'Pending';
                        $transaction->save();
                        $order = ShopOrder::where('id', $transaction->order_id)->first();
                        $order->status = 'Pending';
                        $order->save();
                    }

                    break;
                case 'FAILED':

                    $transaction = ShopOrderPayment::where('ref', $id)->first();
                    if ($transaction) {
                        $transaction->status = 'Cancelled';
                        $transaction->save();
                        $order = ShopOrder::where('id', $transaction->order_id)->first();
                        $order->status = 'Cancelled';
                        $order->save();
                    }

                    //empty cart
                    $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
                    $cart->status = 0;
                    $cart->save();
                    break;
            }

            return view('user.sumup_mobile', ['session' => $tx_status]);
        } catch (\Error $e) {
            Log::error($e->getMessage(), [$e]);
            //TODO:implement what is shown when it fails to retrive stripe data
        }
    }
}
