<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\product\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingFee extends Controller
{
    public function estimateItemShippingCost(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'location_id' => 'required'
        ]);

        try {
            $product = ProductModel::where('product_id', $request->product_id)->first();
            $o = Location::find($product->ships_from);
            $d = Location::where('id', $request->location_id)->first();
            $shipping_data = [
                "width" => $product->width,
                "height" => $product->height,
                "weight" => $product->weight,
                "length" => $product->length,
                "count" => 1,
                "item_desc" => substr($product->product_name, 0, 24),
                "item_value" => $product->product_price,
                "origin_city" => $o->name,
                "dest_city" => $d->name,
                "origin_zip" => $o->zip,
                "dest_zip" => $d->zip,
                "origin_country" => $o->country_code,
                "dest_country" => $d->country_code
            ];

            // dd($shipping_data);

            $shipping_cost = \App\Http\Controllers\ProductController::estimate_shipping($shipping_data);

            return response()->json($shipping_cost);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => 'Failed to estimate cost.'], 500);
        }
    }

    public function estimateItemShippingCostByCity(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'city' => 'required',
            'country_code' => 'required'
        ]);

        try {
            $product = ProductModel::where('product_id', $request->product_id)->first();
            $o = Location::find($product->ships_from);
            $d = Location::where('name', 'LIKE', '%' . $request->city . '%')
                ->where('country_code', $request->country_code)->first();

            if ($o && $d) {
                $shipping_data = [
                    "width" => $product->width,
                    "height" => $product->height,
                    "weight" => $product->weight,
                    "length" => $product->length,
                    "count" => 1,
                    "item_desc" => substr($product->product_name, 0, 24),
                    "item_value" => $product->product_price,
                    "origin_city" => $o->name,
                    "dest_city" => $d->name,
                    "origin_zip" => $o->zip,
                    "dest_zip" => $d->zip,
                    "origin_country" => $o->country_code,
                    "dest_country" => $d->country_code
                ];

                // dd($shipping_data);

                $shipping_cost = \App\Http\Controllers\ProductController::estimate_shipping($shipping_data);

                return response()->json($shipping_cost);
            } else {
                return response()->json(['message' => 'Specified location not supported'], 400);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['message' => 'Failed to estimate cost.'], 500);
        }
    }
}
