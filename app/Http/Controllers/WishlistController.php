<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->paginate(10);
        return view('backend.profile.wishlist.index', ['wishlist' => $wishlist]);
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
        $request->validate([
            'product_id' => 'required',
        ]);

        try {
            $c = Wishlist::where('item_id', $request->product_id)->where('user_id', Auth::id())->first();
            if (!$c) {
                $add = new Wishlist;
                $add->user_id = Auth::id();
                $add->item_id = $request->product_id;
                $add->save();
                return back()->with('success', "Wishlist Item added");
            } else {
                $c->delete();
                return back()->with('success', "Wishlist Item removed");
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to add wishlist Item');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Wishlist  $Wishlist
     * @return \Illuminate\Http\Response
     */
    public function show(Wishlist $Wishlist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function edit(Wishlist $wishlist)
    {
        return view('backend.profile.wishlist.edit', ['list' => $wishlist]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wishlist $wishlist)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Wishlist  $wishlist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wishlist $wishlist)
    {
        try {
            // Check if the wishlist exists
            if (!$wishlist) {
                throw new \Exception('wishlist item not found.');
            }

            // Delete the address
            $wishlist->delete();

            return back()->with('success', 'wishlist item deleted successfully.');
        } catch (\Exception $e) {
            // Handle errors
            Log::error($e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Failed to delete wishlist item. Please try again later.');
        }
    }
}
