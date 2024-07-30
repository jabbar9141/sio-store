<?php

namespace App\Http\Controllers;

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
        $addresses = Address::where('user_id', Auth::id())->paginate(6);
        return view('backend.profile.address.index', ['addresses' => $addresses]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.profile.address.create');
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
            $add->user_id = Auth::id();
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

            return back()->with('success', "Address added");
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to add address');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function edit(Address $address)
    {
        return view('backend.profile.address.edit', ['address' => $address]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
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
            $add =  $address;
            $add->user_id = Auth::id();
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

            return back()->with('success', "Address Updated");
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to update address');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        try {
            // Check if the address exists
            if (!$address) {
                throw new \Exception('Address not found.');
            }

            // Delete the address
            $address->delete();

            return back()->with('success', 'Address deleted successfully.');
        } catch (\Exception $e) {
            // Handle errors
            Log::error($e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Failed to delete address. Please try again later.');
        }
    }
}
