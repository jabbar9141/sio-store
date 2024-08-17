<?php

namespace App\Http\Controllers;

use App\Models\city;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'currencies' => Currency::orderBy('id', 'desc')->get(),
        ];

        return view('backend.admin.currency.index', $data);
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
        try {
            $mainCurrency = $request->input('main_currency');
            $country = $request->input('country');
            $countryCode = $request->input('country_code');
            $currencySymbol = $request->input('currency_symbol');
            $exchangeRate = $request->input('exchange_rate');
            Currency::create([
                'main_currency' => $mainCurrency,
                'country' => $country,
                'country_code' => $countryCode,
                'currency_symbol' => $currencySymbol,
                'currency_rate' => $exchangeRate,
                'status' => true
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Currency added successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'msg' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        // dd($request->all());
        try {
            $currency = Currency::findOrFail($request->currency_id);
            $currency->main_currency = $request->main_currency;
            $currency->country = $request->country;
            $currency->country_code = $request->country_code;
            $currency->currency_symbol = $request->currency_symbol;
            $currency->currency_rate = $request->exchange_rate;
            $currency->save();

            return response()->json([
                'success' => true,
                'msg' => 'Currency updated successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'msg' => 'Something went wrong'
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Currency::where('id', $id)->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Currency deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'msg' => 'Something went wrong'
            ]);
        }
    }

    public function getAllCurrencies()
    {
        try {
            $currencies = Currency::select(
                'id',
                'main_currency',
                'country',
                'country_code',
                'currency_symbol',
                'currency_rate',
                'status'
            )->get();
            return response()->json([
                'status' => true,
                'data' => $currencies
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }


    public function getAllCountries()
    {
        try {
            $countries = Country::select(
                'id',
                'name',
                'iso2',
                'iso3',
                'currency_symbol',
            )->get();
            return response()->json([
                'status' => true,
                'data' => $countries
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }


    public function getAllCitiesOfCountry($countryId)
    {
        try {
            $country = Country::find($countryId);
            $countries = city::select(
                'id',
                'name',
                'state_id',
                'country_id',
                'state_code',
                'country_code'
            )->where('country_id',$country->id)->get();
            return response()->json([
                'status' => true,
                'data' => $countries
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => $th->getMessage()
            ], 500);
        }
    }
}
