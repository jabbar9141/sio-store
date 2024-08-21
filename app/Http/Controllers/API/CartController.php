<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\product\ProductModel;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{

    public function addItem(Request $request)
    {

        $request->validate([
            'product_id' => 'required|exists:product,product_id',
            'qty' => 'numeric|required|min:1',
            'variations' => 'nullable|array',
            'variation_id' => 'required',
            'weight' => 'required',
        ]);
        try {
            $exist = false;
            $p = ProductModel::where('product_id', $request->product_id)->first();
            if ($request->filled('variation_id')) {
                $variation = ProductVariation::where('product_id', $request->product_id)->where('id', $request->variation_id)->first();
            }
            if ($request->qty > $variation->product_quantity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selected Quantity is not valid quantity for product'
                ], 500);
            }
            $variations = $request->input('variations', []);
            $variation_string = json_encode($variations);

            if (Auth::guard('api')->check()) {
                $user_id = Auth::guard('api')->id();
                $cart = Cart::where('user_id', $user_id)->where('status', 1)->first();
                if (!$cart) {
                    $cart = new Cart();
                    $cart->user_id = $user_id;
                    $cart->metadata = json_encode([
                        [
                            'product_id' => $request->product_id,
                            'variations' => $variation_string,
                            'variation_id' => $request->variation_id,
                            'qty' => $request->qty,
                            'price' => $variation->price * $request->qty,
                            'weight' => $request->weight ?? 1,
                        ]
                    ]);
                    $cart->save();
                } else {

                    $newMeta = json_decode($cart->metadata, true);
                    $exist = false;

                    foreach ($newMeta as &$item) {
                        if ($item['product_id'] == $request->product_id) {
                            $exist = true;
                            $item['qty'] = (int) $item['qty'] + (int) $request->qty; 
                            break;
                        }
                    }

                    if (!$exist) {
                        $newMeta[] = [
                            'product_id' => $request->product_id,
                            'variations' => $variation_string,
                            'variation_id' => $request->variation_id,
                            'price' => $variation->price * $request->qty,
                            'qty' => $request->qty,
                            'weight' => $request->weight ?? 1,
                        ];
                    }

                    $cart->metadata = json_encode($newMeta);
                    $cart->save();
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized User'
                ], 401);
            }

            $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
            if (!$exist) {
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully added item to cart.',
                    'cart' => $cart
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Item incremented in cart.',
                    'cart' => $cart
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to add item to cart.'
            ], 500);
        }
    }

    public function removeItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product,product_id',
            'variations' => 'nullable',
        ]);
        try {
            if (Auth::guard('api')->check()) {
                $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
                if ($cart) {
                    $newMeta = json_decode($cart->metadata, true);
                    $updatedMeta = [];
                    foreach ($newMeta as $item) {
                        if ($item['product_id'] != $request->product_id) {
                            $updatedMeta[] = $item;
                        }
                    }
                    if (count($updatedMeta) == 0) {
                        $cart->status = false;
                    }
                    $cart->metadata = json_encode($updatedMeta);
                    $cart->save();
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            $cart = Cart::where('user_id', Auth::guard('api')->id())->where('status', 1)->first();
            return response()->json([
                'status' => true,
                'message' => 'Successfully removed item from cart.',
                'cart' => $cart
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove item from cart.'
            ], 500);
        }
    }


    public function my_cart(Request $request)
    {
        try {
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
                return response()->json(['status' => false, 'cart' => $cart ?? []]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to get cart.'
            ], 500);
        }
    }

}
