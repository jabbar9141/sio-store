<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\city;
use App\Models\CityShippingCost;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Location;
use App\Models\product\ProductModel;
use App\Models\ProductVariation;
use App\Models\ShippingCost;
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
// use Srmklive\PayPal\Services\PayPal as PayPalClient;
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
            // dd($cart);
            // try {
            // $old_order = ShopOrder::where('user_id', Auth::id())
            //     ->where('metadata', $cart)
            //     ->where('status', 'Pending')
            //     ->orderBy('id', 'DESC')
            //     ->first();
            // if ($old_order) {
            //     $order = $old_order;
            // } else {
            //     $order = new ShopOrder;
            //     $order->order_id = UuidV4::uuid4();
            //     $order->user_id = Auth::id();
            //     $order->metadata = json_encode($cart);
            //     $order->save();

            //     foreach ($cart as $i) {
            //         $t = new ShopOrderItem;
            //         $t->order_id = $order->id;
            //         $t->item_id = $i['product_id'];
            //         $t->variant = $i['variations'] ?? '';
            //         $t->price = $i['price'] / $i['qty'];
            //         $t->qty = $i['qty'] ?? 1;
            //         $t->total_price = $i['price'];
            //         $t->product_variation_id = $i['variation_id'] ?? null;
            //         $t->save();
            //     }
            // }

            $order = $cart;
            $addr = Address::where('user_id', Auth::id())->where('status', 1)->paginate(10);

            return view('user.init_order', compact('order', 'addr'));
            // } catch (\Exception $e) {
            //     Log::error($e->getMessage(), [$e]);
            //     return back()->with(['error' => "Failed to initialize order, please try again later."]);
            // }
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
            'country_iso_2' => 'required',
            'products' => 'required'
        ]);


        $session_cart = session('cart');
        $cart_total = 0;
        $total_shipping_cost = 0;
        $vendor_and_weight = [];


        foreach ($session_cart as $it) {
            $the_product = MyHelpers::getProductById($it['product_id']);
            $variation = ProductVariation::find($it['variation_id']) ?? $the_product->variations[0];

            $price = $variation ? $variation->price : $the_product->product_price;

            $available_regions = json_decode($the_product->available_regions, true);
            $total_weight_of_quantities = $it['qty'] * $variation->weight;

            // Sum up the weights per vendor
            if (array_key_exists($the_product->vendor_id, $vendor_and_weight)) {
                $vendor_and_weight[$the_product->vendor_id] += $total_weight_of_quantities;
            } else {
                $vendor_and_weight[$the_product->vendor_id] = $total_weight_of_quantities;
            }
        }

        // Now, calculate the shipping cost based on vendor and weight
        $country = Country::where('iso2', $request->country_iso_2)->first();
        $sum_shipping_cost = 0;

        foreach ($vendor_and_weight as $vendor_id => $weight) {

            $vendor = \App\Models\VendorShop::find($vendor_id);

            // Determine the vendor's country
            if ($vendor->user->currency) {
                $vendor_country = Country::where('name', 'like', $vendor->user->currency->country)->first();
            } else {
                $vendor_country = Country::where('name', 'like', 'Italy')->first();
            }

            // Check if the vendor's country is the same as the request country
            if ($vendor_country->id == $country->id) {
                $city_percentage = CityShippingCost::where('city_id', (int) session('city_id'))->first()?->percentage;
                $total_shipping = ShippingCost::where('country_iso_2', $vendor_country->iso2)
                    ->where('weight', $weight)
                    ->first()?->cost;

                // Calculate the shipping cost considering city percentage if available
                if ($city_percentage && $total_shipping) {
                    $shipping_cost = number_format(($city_percentage * $total_shipping) / 100, 2);
                } else {
                    $shipping_cost = $total_shipping;
                }
            } elseif (in_array('global', $available_regions)) {
                $shipping_cost = ShippingCost::where('country_iso_2', $vendor_country->iso2)
                    ->where('weight', $weight)
                    ->first()?->cost;
            } else {
                $countries_origins = Country::whereIn('id', $available_regions)
                    ->pluck('id')
                    ->toArray();

                if (in_array($country->id, $countries_origins)) {
                    $shipping_cost = ShippingCost::where('country_iso_2', $vendor_country->iso2)
                        ->where('weight', $weight)
                        ->first()?->cost;
                } else {
                    $shipping_cost = 0;
                }
            }

            // Sum up the shipping cost
            $sum_shipping_cost += $shipping_cost;
        }

        // Return the response with the calculated shipping cost and other relevant details
        return response()->json([
            'success' => $sum_shipping_cost > 0 ? true : false,
            'shipping_cost' => $sum_shipping_cost,
            'shipping_plus_total' => MyHelpers::fromEuroView(session('currency_id', 0), $sum_shipping_cost + $request->euro_cart_total),
            'euro_shipping_cost' => MyHelpers::fromEuroView(session('currency_id', 0), $sum_shipping_cost),
            'euro_shipping_plus_total' => $sum_shipping_cost + $request->euro_cart_total,
            'message' => $sum_shipping_cost > 0 ? 'Success' : 'Error Calculating Shipping Cost',
        ]);


        // Todo: Previous Code
        /*  $products = $request->has('products') ? json_decode($request->products, true) : null;
        $country = Country::where('iso2', $request->country_iso_2)->first();

        $sum_shipping_cost = 0;

        foreach ($products as $product) {
            $request_product_weight = $product['weight'];
            $product = ProductModel::find($product['product_id']);
            if ($product) {
                $first_variant = ProductVariation::find($product['product_variation_id']);
                $available_regions = json_decode($product->available_regions);
                if (!is_array($available_regions)) {
                    $available_regions = json_decode($available_regions, true);
                }

                // dd($product, json_decode($product->available_regions), in_array('global', $available_regions));

                if ($product->vendor->user->currency) {
                    $vendor_country = Country::where('name', 'like', $product->vendor->user->currency->country)->first();
                } else {
                    $vendor_country = Country::where('name', 'like', 'Italy')->first();
                }

                // dd($vendor_country->id ,$product->vendor->user->currency, (int)session('country_id'));

                if ($vendor_country->id == $country->id) {
                    $city_percentage = CityShippingCost::where('city_id', (int)session('city_id'))->first()?->percentage;
                    $total_shipping = ShippingCost::where('country_iso_2', $vendor_country->iso2)->where('weight', $request_product_weight)->first()?->cost;
                    if ($city_percentage && $total_shipping) {
                        $shipping_cost = number_format(($city_percentage * $total_shipping) / 100, 2);
                    } else {
                        $shipping_cost = $total_shipping;
                    }
                } elseif (in_array('global', $available_regions)) {
                    $shipping_cost = ShippingCost::where('country_iso_2', $vendor_country->iso2)->where('weight', $request_product_weight)->first()?->cost;
                } else {
                    $countries_origins = Country::whereIn('id', $available_regions)->pluck('id')->toArray();
                    if (in_array($country->id, $countries_origins)) {
                        $shipping_cost = ShippingCost::where('country_iso_2', $vendor_country->iso2)->where('weight', $request_product_weight)->first()?->cost;
                    } else {
                        $shipping_cost = 0;
                    }
                }
                $sum_shipping_cost += $shipping_cost;
            }
        }

        // dd($sum_shipping_cost);


        // try {
        // $country_iso = $request->country_iso_2;
        // $weights = $request->weights ?? [];
        // $address_id = $request->address_id;

        // $address = Address::find($address_id);
        // $country = $address->getCountry();
        // $city = city::where('country_id', $country->id)->where('name', $address->city)->first();

        // // dd($address,$country,$city);

        // $euro_shipping_cost = ShippingCost::where('country_iso_2', $country_iso)->whereIn('weight', $weights)->sum('cost') ?? 0;
        // $euro_shipping_plus_total = $request->euro_cart_total + $euro_shipping_cost;
        // $euro_cart_total = MyHelpers::fromEuroView(session('currency_id', 0), $request->euro_cart_total);

        // $shipping_cost = MyHelpers::fromEuroView(session('currency_id', 0), $euro_shipping_cost);
        // $shipping_plus_total = MyHelpers::fromEuroView(session('currency_id', 0), $euro_shipping_plus_total);


        return response()->json([
            'success' => $shipping_cost > 0 ? true : false,
            'shipping_cost' => $sum_shipping_cost,
            'shipping_plus_total' => MyHelpers::fromEuroView(session('currency_id', 0), $sum_shipping_cost + $request->euro_cart_total),
            'euro_shipping_cost' => MyHelpers::fromEuroView(session('currency_id', 0), $sum_shipping_cost),
            'euro_shipping_plus_total' => $sum_shipping_cost + $request->euro_cart_total,
            'message' => $shipping_cost > 0 ? 'Success' : 'Error Calculating Shipping Cost',
        ]);

        */


        // $destination = Address::find($request->get('address_id'));
        // $o = ShopOrder::where('id', $request->get('order_id'))->first();

        // $items = $o->items;
        // $shipping_costs = []; // Initialize array to store shipping costs for each provider

        // foreach ($items as $item) {
        //     $product = ProductModel::where('product_id', $item->item_id)->first();
        //     if (null != $product->ships_from) {
        //         $origin = Location::find($product->ships_from);
        //         $shipping_data = [
        //             "width" => $product->width,
        //             "height" => $product->height,
        //             "weight" => $product->weight,
        //             "length" => $product->length,
        //             "count" => $item->qty,
        //             "item_desc" => substr($product->product_name, 0, 24),
        //             "item_value" => $item->price ?? $item->variation->price, // changed ($item->price ?? $product->product_price,) to ($item->price ?? $item->variation->price,)
        //             "origin_city" => $origin->name,
        //             "dest_city" => $destination->city,
        //             "origin_zip" => $origin->zip,
        //             "dest_zip" => $destination->zip,
        //             "origin_country" => $origin->country_code,
        //             "dest_country" => $destination->country
        //         ];

        //         $shipping_cost = ProductController::estimate_shipping($shipping_data);
        //         $shipping_cost = ((array) json_decode($shipping_cost));

        //         // Accumulate shipping costs for each provider
        //         foreach ($shipping_cost as $provider => $cost) {
        //             if (isset($shipping_costs[$provider])) {
        //                 $shipping_costs[$provider] += (($cost != '') ? $cost : 0);
        //             } else {
        //                 $shipping_costs[$provider] = (($cost != '') ? $cost : 0);
        //             }
        //         }
        //     }
        // }
        // //save the estimation in the db
        // $o->shipping_cost = $shipping_costs;
        // $o->save();

        // $markup = '';
        // if (count($shipping_costs) > 0) {
        //     foreach ($shipping_costs as $provider => $cost) {
        //         if ($cost > 0) {
        //             $provider_upper = strtoupper($provider);
        //             $markup .= '
        //                 <div class="form-check">
        //                     <input class="form-check-input" type="radio" name="shipping_provider" id="' . $provider . '" onchange="calculate_total(this)"
        //                         value="' . $provider_upper . '" data-cost="' . $cost . '" required>
        //                     <label class="form-check-label" for="' . $provider . '">' . $provider_upper . ' - ' . MyHelpers::fromEuro(Auth::user()->currency_id, $cost) . '</label>
        //                 </div>
        //             ';
        //         }
        //     }
        // }

        // // Output the cumulative shipping costs for each provider
        // return response()->json([
        //     'shipping_costs' => $shipping_costs,
        //     'markup' => $markup,
        // ], 200);
        // } catch (Exception $e) {
        //     Log::error($e->getMessage(), [$e]);
        //     return response()->json(['err' => 'an error occurred while calculating shipping fees for the order'], 500);
        // }
    }

    public function submit(Request $request)
    {
        $request->validate([
            'billing_address' => 'required',
            // 'shipping_provider' => 'required',
            'shipping_cost' => 'required',
            'payment' => 'required'
        ]);
        try {

            $cart = session('cart');
            $order = new ShopOrder;
            $order->order_id = UuidV4::uuid4();
            $order->user_id = Auth::id();
            $order->shipping_cost = $request->shipping_cost;
            $order->save();

            $order_id = $order->id;

            if (($cart) && count($cart) > 0) {
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

            // $order = ShopOrder::where('id', $order_id)->first();

            $order->billing_address_id = $request->billing_address;
            $order->shipping_address_id = $request->billing_address;
            $order->shipping_method = $request->shipping_provider;
            $order->payment_method = $request->payment;
            $order->save();

            // $order->shipping_cost = (array) json_decode($order->shipping_cost);
            $YOUR_DOMAIN = env('APP_URL');

            // dd($order->items);
            // DB::beginTransaction();

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
                    $cost = $cost + ($price);
                }

                //one last item to represent the shipping costs
                // $cost = $cost + ($order->shipping_cost[strtolower($request->shipping_provider)] * 100);
                $cost = $cost + ($order->shipping_cost);

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
                        $cost,
                        'EUR',
                        $order_id . "_" . rand(100000, 999999),
                        env('SUMUP_EMAIL'),
                        'Payment Intent for order: ' . $order_id,
                        Auth::user()->email,
                        $YOUR_DOMAIN . '/payment-success-sumup?session_id={CHECKOUT_SESSION_ID}'
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
            } elseif ($request->payment == "PAYPAL") {
                $cost = 0;

                foreach ($order->items as $it) {
                    $price = $it->total_price ?? 0; //
                    if ($price == 0) {
                        $price = $it->variation->price * $it->qty;
                    }
                    $cost = $cost + $price;
                }
                $cost = ($cost + $order->shipping_cost);

                return $this->createPayment($cost, $order->id);
            } elseif ($request->payment == "PAYSTACK") {
                $cost_to_pay = 0;

                foreach ($order->items as $it) {
                    $price = $it->total_price ?? 0;
                    if ($price == 0) {
                        $price = $it->variation->price * $it->qty;
                    }
                    $cost_to_pay = $cost_to_pay + $price;
                }
                $cost_to_pay = ($cost_to_pay + $order->shipping_cost);
                return $this->createPayStackPayment($cost_to_pay, $order->id);
            } else {
                // DB::rollBack();
                return back()->with(['error' => "Failed to initiate payment, please try again later"]);
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

            if ($request->order_status === 'Cancelled') {

                $item->variation->update([
                    'product_quantity' => $item->variation->product_quantity + $item->qty,
                ]);
            }


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

        $country_iso = $request->country_iso_2;
        $weights = $item->variation->weight ?? [];

        $euro_shipping_cost = ShippingCost::where('country_iso_2', $dest_address->country)->whereIn('weight', [$weights])->sum('cost') ?? 0;
        $euro_shipping_plus_total = $request->euro_cart_total + $euro_shipping_cost;
        $euro_cart_total = MyHelpers::fromEuroView(session('currency_id', 0), $request->euro_cart_total);

        $shipping_cost = MyHelpers::fromEuroView(session('currency_id', 0), $euro_shipping_cost);
        $shipping_plus_total = MyHelpers::fromEuroView(session('currency_id', 0), $euro_shipping_plus_total);


        // return response()->json([
        //     'success' => $shipping_cost > 0 ? true : false,
        //     'shipping_cost' => $shipping_cost,
        //     'shipping_plus_total' => $shipping_plus_total,
        //     'euro_shipping_cost' => $euro_shipping_cost,
        //     'euro_shipping_plus_total' => $euro_shipping_plus_total,
        //     'message' => $shipping_cost > 0 ? 'Success' : 'Error Calculating Shipping Cost',
        // ]);

        // $response = Http::withHeaders(['Accept' => 'application/json'])->post(env('SHIPPING_POST_API_URL'), [
        //     'width' => $product->width,
        //     'height' => $product->height,
        //     'weight' => $product->weight,
        //     'length' => $product->length,
        //     'count' => $item->qty,
        //     'item_desc' => $product->product_name,
        //     'item_value' => $product->product_price,
        //     'origin_country' => $origin->country_code,
        //     'origin_zip' => $origin->zip,
        //     'origin_city' => $origin->name,
        //     'origin_name' => $origin_vendor->name . '(' . $origin_vendor_shop->shop_name . ')',
        //     'origin_address' => $origin_vendor->address,
        //     'origin_email' => $origin_vendor->email,
        //     'pickup_date' => $request->pickup_date,
        //     'dest_city' => $dest_address->city,
        //     'dest_zip' => $dest_address->zip,
        //     'dest_email' => $dest_address->email,
        //     'dest_phone' => $dest_address->phone,
        //     'dest_address' => $dest_address->address1,
        //     'dest_address2' => $dest_address->address2,
        //     'dest_name' => $dest_address->firstname . ' ' . $dest_address->lastname,
        //     'dest_country' => $dest_address->country,
        //     'dest_state' => $dest_address->state,
        //     'ref' => $item->order->order_id . '_' . $item->id,
        // ]);

        // if ($response->successful()) {
        //     $body = $response->json();
        $item->tracking_id = rand(500000, 50000000);
        $item->status = 'Shipped';
        $item->save();

        return back()->with(['success' => 'Success']);
        // } else {
        //     Log::error($response->json()['message'], [$response->json()]);
        //     return back()->with(['error' => $response->json()['message']]);
        // }
    }

    public function createPayment($price, $order_id)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->setCurrency('EUR');
        $formattedPrice = number_format($price, 2, '.', '');
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('success.payment', ['order_id' => $order_id]),
                "cancel_url" => route('cancel.payment', ['order_id' => $order_id]),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" => $formattedPrice
                    ],
                ],
            ],
        ]);
        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
            $order = ShopOrder::find($order_id);
            if ($order) {
                // $order->items->delete();
                ShopOrderItem::where('order_id', $order->id)->delete();
                $order->delete();
            }
            return back()->with(['error' => $response['message'] ?? 'Something went wrong']);
        } else {
            $order = ShopOrder::find($order_id);
            if ($order) {
                // $order->items->delete();
                ShopOrderItem::where('order_id', $order->id)->delete();
                $order->delete();
            }
            return back()->with(['error' => $response['message'] ?? 'Something went wrong']);
        }
    }

    public function success(Request $request, $order_id)
    {

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        $order = ShopOrder::find($order_id);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $lp = new ShopOrderPayment;
            $lp->ref = $response['id'];
            $lp->payment_method = 'PAYPAL';
            $lp->order_id = $order->id;
            $lp->amount = (float)($order->items->sum('total_amount') ?? 0) + (float)($order->shipping_cost ?? 0);
            $lp->metadata = $order->metadata;
            $lp->status = 'Done';
            $lp->save();

            $order->status = 'Completed';
            $order->save();

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

            return redirect('/')->with(['success' => 'Payment Successful !']);
        } else {
            if ($order) {
                // $order->items->delete();
                ShopOrderItem::where('order_id', $order->id)->delete();
                $order->delete();
            }
            return back()->with(['error' => $response['message'] ?? 'Something went wrong']);
        }
    }

    public function getExchangeRate()
    {
        $response = Http::get('https://www.google.com/search?q=1+EUR+to+NGN');

        if ($response->successful()) {
            $body = $response->body();

            // Use a regex pattern to extract the exchange rate from the Google search result page
            preg_match('/<div class="BNeawe iBp4i AP7Wnd">([\d,\.]+) Nigerian Naira<\/div>/', $body, $matches);

            if (isset($matches[1])) {
                // Convert the extracted rate to a float and remove any commas
                return floatval(str_replace(',', '', $matches[1]));
            }
        }

        return null;
    }

    public function createPayStackPayment($price, $order_id)
    {
        // $exchangeRate = $this->getExchangeRate();
        $currency = Currency::where('country', 'like', 'Nigeria')->first();
        $exchangeRate = $currency->currency_rate ?? 1;
        // $formattedPrice = number_format($price, 2, '.', '');
        if ($exchangeRate) {
            $amountInNaira = ($price * $exchangeRate); // Amount in kobo
            $formattedPrice = number_format($amountInNaira, 2, '.', '');

            $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))->post('https://api.paystack.co/transaction/initialize', [
                'email' => Auth::user()->email,
                'amount' => $formattedPrice * 100,
                'callback_url' => 'https://www.siostore.eu/paystack.callback/?order_id=' . $order_id, // route('paystack.callback', ['order_id' => $order_id])
            ]);

            $data = $response->json();

            if ($data['status']) {
                return redirect($data['data']['authorization_url']);
            } else {
                $order = ShopOrder::find($order_id);
                if ($order) {
                    $order->items->delete();
                    $order->delete();
                }
                return back()->with('error', 'Unable to initiate payment. Please try again.');
            }
        } else {
            $order = ShopOrder::find($order_id);
            if ($order) {
                // $order->items->delete();
                ShopOrderItem::where('order_id', $order->id)->delete();
                $order->delete();
            }
            return back()->with('error', 'Unable to fetch exchange rate. Please try again.');
        }
    }

    public function payStackCallback(Request $request)
    {
        $reference = $request->query('reference');
        $order_id = $request->query('order_id');

        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))->get("https://api.paystack.co/transaction/verify/{$reference}");

        $data = $response->json();

        if ($data['status'] && $data['data']['status'] == 'success') {
            // Payment was successful
            $order = ShopOrder::find($order_id);

            if ($order) {
                $lp = new ShopOrderPayment;
                $lp->ref = $data['data']['reference'];
                $lp->payment_method = 'PAYSTACK';
                $lp->order_id = $order->id;
                $lp->amount = (float)($order->items->sum('total_amount') ?? 0) + (float)($order->shipping_cost ?? 0);
                $lp->metadata = $order->metadata;
                $lp->status = 'Done';
                $lp->save();

                $order->status = 'Completed';
                $order->save();

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

                // Empty cart
                $cart = Cart::where('user_id', Auth::id())->where('status', 1)->first();
                if ($cart) {
                    $cart->status = 0;
                    $cart->save();
                }
                session()->forget('cart');
            }

            return redirect()->route('payment.success');
        } else {
            $order = ShopOrder::find($order_id);
            if ($order) {
                ShopOrderItem::where('order_id', $order->id)->delete();
                // $order->items->delete();
                $order->delete();
            }
            // Payment failed
            return redirect()->route('payment.failed');
        }
    }

    public function cancel($order_id)
    {
        $order = ShopOrder::find($order_id);
        if ($order) {
            // $order->items->delete();
            ShopOrderItem::where('order_id', $order->id)->delete();
            $order->delete();
        }
        return back()->with(['error' => $response['message'] ?? 'Payment Unsuccessfull']);
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
