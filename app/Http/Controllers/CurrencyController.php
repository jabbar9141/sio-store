<?php

namespace App\Http\Controllers;

use App\Models\Currency;
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
}
