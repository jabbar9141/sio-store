<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addresses = Address::where('user_id', Auth::guard('api')->id())->paginate(10);
        // Return paginated JSON response
        return response()->json([
            'data' => $addresses->items(),
            'current_page' => $addresses->currentPage(),
            'per_page' => $addresses->perPage(),
            'total' => $addresses->total(),
            'last_page' => $addresses->lastPage(),
            'next_page_url' => $addresses->nextPageUrl(),
            'previous_page_url' => $addresses->previousPageUrl()
        ]);
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
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'email|required',
            'phone' => 'required',
            'address1' => 'required',
            'country' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required'
        ]);

        try {
            $add = new Address;
            $add->user_id = Auth::guard('api')->id();
            $add->firstname = $request->firstname;
            $add->lastname = $request->lastname;
            $add->email = $request->email;
            $add->phone = $request->phone;
            $add->address1 = $request->address1;
            $add->address2 = $request->address2;
            $add->country = $request->country;
            $add->city = $request->city;
            $add->state = $request->state;
            $add->zip = $request->zip;
            $add->save();

            return response()->json(['message' => "Address added"]);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => 'Failed to add address'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $address)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'email|required',
            'phone' => 'required',
            'address1' => 'required',
            'country' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required'
        ]);

        try {
            $add = Address::where('user_id', Auth::guard('api')->id())->where('id', $address)->first();
            if ($add) {
                $add->user_id = Auth::guard('api')->id();
                $add->firstname = $request->firstname;
                $add->lastname = $request->lastname;
                $add->email = $request->email;
                $add->phone = $request->phone;
                $add->address1 = $request->address1;
                $add->address2 = $request->address2;
                $add->country = $request->country;
                $add->city = $request->city;
                $add->state = $request->state;
                $add->zip = $request->zip;
                $add->save();

                return response()->json(['message' => "Address updated"]);
            } else {
                return response()->json(['message' => 'address not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => 'Failed to update address'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function destroy($address)
    {
        try {
            // Check if the address exists
            $address = Address::where('user_id', Auth::guard('api')->id())->where('id', $address)->first();

            if (!$address) {
                return response()->json(['message' => 'address not found'], 404);
            }

            // Delete the address
            $address->delete();

            return response()->json(['success' => 'Address deleted successfully.']);
        } catch (\Exception $e) {
            // Handle errors
            Log::error($e->getMessage(), ['exception' => $e]);

            return response()->json(['message' => 'Failed to delete address'], 500);
        }
    }
}
