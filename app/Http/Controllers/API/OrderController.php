<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CityShippingCost;
use App\Models\Country;
use App\Models\Location;
use App\Models\product\ProductModel;
use App\Models\ProductVariation;
use App\Models\ShippingCost;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopOrderPayment;
use App\MyHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

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
            "status" => true,
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
        // return Auth::guard('api')->id();
        $request->validate([
            'cart_id' => 'required',
            'country_iso_2' => 'nullable'
        ]);

        $cart = Cart::where('user_id', Auth::guard('api')->id())->where('id', $request->cart_id)->where('status', 1)->first();
        if (isset($cart)) {
            $order = $cart;
          
            $vendor_and_weight = [];
            $sum_shipping_cost = 0;
            if ($request->filled('country_iso_2')) {
                $meta =  json_decode($cart->metadata);

                foreach ($meta as $it) {
                    $the_product = MyHelpers::getProductById($it->product_id);
                    $variation = ProductVariation::find($it->variation_id) ?? $the_product->variations[0];

                    $price = $variation ? $variation->price : $the_product->product_price;

                    $available_regions = json_decode($the_product->available_regions, true);

                    if (!is_array($available_regions)) {
                        $available_regions = json_decode($available_regions, true);
                    }

                    $total_weight_of_quantities = $it->qty * $variation->weight;

                    // Sum up the weights per vendor
                    if (array_key_exists($the_product->vendor_id, $vendor_and_weight)) {
                        $vendor_and_weight[$the_product->vendor_id] += $total_weight_of_quantities;
                    } else {
                        $vendor_and_weight[$the_product->vendor_id] = $total_weight_of_quantities;
                    }
                }

                // Now, calculate the shipping cost based on vendor and weight
                $country = Country::where('iso2', $request->country_iso_2)->first();


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
                    $sum_shipping_cost += $shipping_cost;
                }
                
                $newMeta = [];
                foreach (json_decode($order->metadata) as $item) {
                    $p = ProductModel::find($item->product_id);
                    $item->product_data = $p;
                    array_push($newMeta,  [$item]);
                }
                $order->metadata = $newMeta;
            }
           
            return response()->json([
                'status' => true,
                'message' => 'Order Initialized',
                'order' => $order,
                'shipping_cost' => $sum_shipping_cost,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => "Cart Not Found",
            ]);
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

    public function submit(Request $request)
    {  
        $request->validate([
            'cart_id' =>  'required',
            'payment_provider' => 'required',
            'shipping_address_id' => 'required',
            'shipping_cost' => 'required',
        ]);
       
        try {
            DB::beginTransaction();
            $cart = Cart::find($request->cart_id);
           
            if (isset($cart)) {
                $order = new ShopOrder;
                $order->order_id = UuidV4::uuid4();
                $order->user_id = Auth::guard('api')->id();
                $order->shipping_cost = $request->shipping_cost;
                $order->billing_address_id = $request->shipping_address_id;
                $order->shipping_address_id = $request->shipping_address_id;
                $order->shipping_method =  null;
                $order->payment_method = $request->payment_provider;
                $order->status = 'Completed';
                $order->metadata = $cart->metadata;
                $order->save();
                
                $cartMeta = json_decode($cart->metadata);
                foreach ($cartMeta as $i) {
                    $t = new ShopOrderItem;
                    $t->order_id = $order->id;
                    $t->item_id = $i->product_id;
                    $t->variant = $i->variations ?? '';
                    $t->price = $i->price / $i->qty;
                    $t->qty = $i->qty ?? 1;
                    $t->total_price = $i->price;
                    $t->product_variation_id = $i->variation_id ?? null;
                    $t->save();
                }


                $lp = new ShopOrderPayment;
                $lp->ref = UuidV4::uuid4();
                $lp->payment_method = $request->payment_provider;
                $lp->order_id = $order->id;
                $lp->amount = (float)($order->items->sum('total_amount') ?? 0) + (float)($order->shipping_cost ?? 0);
                $lp->metadata = $order->metadata;
                $lp->status = 'Done';
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

                $cart->status = 0;
                $cart->save();
            }
            DB::commit();
            return response()->json(
                [   
                    'status' => true,
                    'message' => 'Payment successful, Order completed'
                ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), [$e]);
            return response()->json(
                [    
                    'stauts' => false,
                    'message' => "Something went wrong !",
                ], 500);
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

    public function cancel($cart_id)
    {   
        return 'sdafsa';
        $card = Cart::find($cart_id);
        if ($card) {
            $cart_id->delete();
        }
        return response()->json(
            [
                'status' => 'FAILED',
                'message' => 'Payment Un-successfull'
            ]
        );
    }

    public function success(Request $request)
    {  
        return 'sdafsa';
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            return response()->json(
                [
                    'status' => 'COMPLETED',
                    'message' => 'Payment Successfully'
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => 'FAILED',
                    'error' => $response['message'] ?? 'Something went wrong'
                ]
            );
        }
    }
}
