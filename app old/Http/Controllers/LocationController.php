<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * set shipping location for user session
     */
    public function set_ship_loc(Request $request, $location_id)
    {
        $l = Location::find($location_id);
        session(['ship_to' => $location_id, 'ship_to_str' => $l->name . ', ' . $l->country_code]);
        return back()->with(['success' => 'Location set']);
    }

    public function search_locations(Request $request)
    {
        $request->validate([
            'query' => 'nullable'
        ]);

        if ($request->get('query') != '') {
            $q = Location::where('name', 'LIKE', "%" . $request->get('query') . '%')
                ->orWhere('country_code', 'LIKE', "%" . $request->get('query') . '%')->limit(10)->get();
        } else {
            $q = Location::all();
        }

        return $q;
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
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        //
    }
}
