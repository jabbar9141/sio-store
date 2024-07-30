<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\product\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{

    public function addItem(Request $request)
    {
        // Validation rules
        $request->validate([
            'product_id' => 'required|exists:product,product_id', // Make sure the table name and column name are correct
            'qty' => 'numeric|required|min:1',
            'variations' => 'nullable|array'
        ]);
        try {
            $exist = false;
            $p = ProductModel::where('product_id', $request->product_id)->first();
            // Process variations
            $variations = $request->input('variations', []);
            $variation_string = json_encode($variations); // Convert array to JSON string
            // Check if the user is authenticated
            if (Auth::guard('api')->check()) {
                $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();

                if (!$cart) {
                    // If the user doesn't have a cart, create a new one
                    $cart = new Cart();
                    $cart->user_id = Auth::guard('api')->id();
                    $cart->metadata = json_encode([
                        [
                            'product_id' => $request->product_id,
                            'variations' => $variation_string, // Store variations as a JSON string
                            'qty' => $request->qty,
                            'price' => $p->product_price
                        ]
                    ]);
                    $cart->save();
                } else {
                    // If the user has a cart, update database carts
                    $newMeta = json_decode($cart->metadata, true);

                    foreach ($newMeta as &$item) {
                        if ($item['product_id'] == $request->product_id && $variation_string == $item['variations']) {
                            $exist = true;
                            $item['qty'] = $request->qty; // Update quantity
                            break;
                        }
                    }

                    if (!$exist) {
                        $newMeta[] = [
                            'product_id' => $request->product_id,
                            'variations' => $variation_string,
                            'price' => $p->product_price,
                            'qty' => $request->qty,
                        ];
                    }

                    $cart->metadata = json_encode($newMeta);
                    $cart->save();
                }
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
            if (!$exist) {
                return response()->json(['message' => 'Successfully added item to cart.', 'cart' => $cart]);
            } else {
                return response()->json(['message' => 'Item incremented in cart.', 'cart' => $cart]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => 'Failed to add item to cart.'], 500);
        }
    }

    public function removeItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product,product_id',
            'variations' => 'nullable',
        ]);
        try {
            // Check if the user is authenticated
            if (Auth::guard('api')->check()) {
                $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();

                if ($cart) {
                    $newMeta = json_decode($cart->metadata, true);
                    $updatedMeta = [];

                    // Filter out the item to be removed
                    foreach ($newMeta as $item) {

                        if ($item['product_id'] != $request->product_id || json_encode($request->variations ?? []) != ($item['variations'])) {
                            $updatedMeta[] = $item;
                        }
                    }

                    // Update the database cart
                    $cart->metadata = json_encode($updatedMeta);
                    $cart->save();
                }
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();

            return response()->json(['message' => 'Successfully removed item from cart.', 'cart' => $cart]);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => 'Failed to remove item from cart.'], 500);
        }
    }


    public function my_cart(Request $request)
    {
        try {
            // Check if the user is authenticated
            if (Auth::guard('api')->check()) {
                $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
                if (!empty($cart)) {
                    $newMeta = [];
                    foreach (json_decode($cart->metadata) as $item) {
                        $p = ProductModel::find($item->product_id);
                        $item->product_data = $p;
                        array_push($newMeta,  [$item]);
                    }

                    $cart->metadata = $newMeta;
                }
                return response()->json([$cart]);
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => 'Failed to get cart.'], 500);
        }
    }
}
