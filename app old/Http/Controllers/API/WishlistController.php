<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        try {
            $t = Wishlist::where('item_id', $request->product_id)->where('user_id', Auth::guard('api')->id())->first();
            if (!$t) {
                $add = new Wishlist;
                $add->user_id = Auth::id();
                $add->item_id = $request->product_id;
                $add->save();
                return response()->json(['message' => 'Item added to wishlist']);
            } else {
                return response()->json(['message' => 'Item already in wishlist']);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => 'Failed to add item to wishlist.'], 500);
        }
    }

    public function removeItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        try {
            $t = Wishlist::where('item_id', $request->product_id)->where('user_id', Auth::guard('api')->id())->first();
            if (!$t) {
                return response()->json(['message' => 'Item not found'], 404);
            } else {
                $t->delete();
                return response()->json(['message' => 'Item removed from wishlist']);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => 'Failed to remove item from wishlist.'], 500);
        }
    }

    public function myWishlist()
    {
        try {
            $t = Wishlist::with(['product'])->where('user_id', Auth::guard('api')->id())->paginate(20);
            return response()->json([
                'data' => $t->items(),
                'current_page' => $t->currentPage(),
                'per_page' => $t->perPage(),
                'total' => $t->total(),
                'last_page' => $t->lastPage(),
                'next_page_url' => $t->nextPageUrl(),
                'previous_page_url' => $t->previousPageUrl()
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => 'Failed to remove item from wishlist.'], 500);
        }
    }
}
