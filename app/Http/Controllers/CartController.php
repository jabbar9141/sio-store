<?php

namespace App\Http\Controllers;

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
            'variations' => 'nullable|array'
        ]);

        try {
            $p = ProductModel::where('product_id', $request->product_id)->first();

            if ($request->filled('variation_id')) {
                $variation = ProductVariation::where('product_id', $request->product_id)->where('id', $request->variation_id)->first();
            }
            if ($request->qty > $variation->product_quantity) {
                return back()->with('error', 'Selected Quantity is not valid quantity for product');
            }
            $variations = $request->input('variations', []);
            $variation_string = json_encode($variations); // Convert array to JSON string
            // dd($variation_string);

            // Check if the user is authenticated
            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->where('status', 1)->first();

                if (!$cart) {
                    // If the user doesn't have a cart, create a new one
                    $cart = new Cart();
                    $cart->user_id = Auth::id();
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
                    // dd($cart);
                } else {
                    // If the user has a cart, update both database and session carts
                    $newMeta = json_decode($cart->metadata, true);
                    $exist = false;

                    foreach ($newMeta as &$item) {
                        if ($item['product_id'] == $request->product_id) {
                            $exist = true;
                            $item['qty'] = (int) $item['qty'] + (int) $request->qty; // Update quantity
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
            }

            // Update session cart
            $sessionCart = session('cart', []);
            $exist = false;

            foreach ($sessionCart as &$item) {
                if ($item['product_id'] == $request->product_id) {
                    $exist = true;
                    $item['qty'] += $request->qty;
                    break;
                }
            }

            if (!$exist) {
                $sessionCart[] = [
                    'product_id' => $request->product_id,
                    'variations' => $variation_string,
                    'variation_id' => $request->variation_id,
                    'qty' => $request->qty,
                    'price' => (float) $variation->price * (int) $request->qty,
                    'weight' => $request->weight ?? 1,
                ];
            }

            session(['cart' => $sessionCart]);

            return back()->with('success', 'Successfully added item to cart.');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to add item to cart.');
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
            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->where('status', 1)->first();

                if ($cart) {
                    $newMeta = json_decode($cart->metadata, true);
                    $updatedMeta = [];

                    // Filter out the item to be removed
                    foreach ($newMeta as $item) {
                        // dd($request->variations, $item['variations']);

                        if ($item['product_id'] != $request->product_id) {
                            $updatedMeta[] = $item;
                        }
                    }

                    // Update the database cart
                    $cart->metadata = json_encode($updatedMeta);
                    $cart->save();
                }
            }

            // Update the session cart
            $sessionCart = session('cart', []);
            $updatedSessionCart = [];


            // Filter out the item to be removed
            foreach ($sessionCart as $item) {
                if ($item['product_id'] != $request->product_id || ($request->variations ?? '') != json_encode($item['variations'])) {
                    $updatedSessionCart[] = $item;
                }
            }

            session(['cart' => $updatedSessionCart]);

            return back()->with('success', 'Successfully removed item from cart.');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to remove item from cart.');
        }
    }


    public function my_cart(Request $request)
    {
        if (session('cart') && count(session('cart')) > 0) {
            return view('user.my_cart');
        } else {
            return redirect()->route('home-page')->withErrors('No Items in cart');
        }
    }
}
