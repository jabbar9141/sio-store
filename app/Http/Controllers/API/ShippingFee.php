<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CityShippingCost;
use App\Models\Country;
use App\Models\Location;
use App\Models\product\ProductModel;
use App\Models\ProductVariation;
use App\Models\ShippingCost;
use App\Models\VendorShop;
use App\MyHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Climate\Product;

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

    public function getShippingCost(Request $request)
    {
        try {
            // return $request->all();
            $product = ProductModel::find($request->product_id);
            $productVariation = ProductVariation::where('id', $request->variation_id)->where('product_id', $product->product_id)->first();

            if ($productVariation) {
                $available_regions = json_decode($product->available_regions);
                if (!is_array($available_regions)) {
                    $available_regions = json_decode($available_regions, true);
                }

                if ($product->vendor->user->currency) {
                    $vendor_country = Country::where('name', 'like', $product->vendor->user->currency->country)->first();
                } else {
                    $vendor_country = Country::where('name', 'like', 'Italy')->first();
                }
                // return $vendor_country;

                if ($vendor_country->id == (int) $request->country_id) {
                    $city_percentage = CityShippingCost::where('city_id', (int) $request->city_id)->first()?->percentage;
                    $total_shipping = ShippingCost::where('country_iso_2', $vendor_country->iso2)->where('weight', $productVariation->weight)->first()?->cost;
                    if ($city_percentage && $total_shipping) {
                        $shipping_cost = number_format(($city_percentage * $total_shipping) / 100, 2);
                    } else {
                        $shipping_cost = $total_shipping;
                    }
                } elseif (in_array('global', $available_regions)) {
                    $shipping_cost = ShippingCost::where('country_iso_2', $vendor_country->iso2)->where('weight', $productVariation->weight)->first()?->cost;
                } else {
                    $countries_origins = Country::whereIn('id', $available_regions)->pluck('id')->toArray();
                    if (in_array((int) $request->country_id, $countries_origins)) {
                        $shipping_cost = ShippingCost::where('country_iso_2', $vendor_country->iso2)->where('weight', $productVariation->weight)->first()?->cost;
                    } else {
                        $shipping_cost = 0;
                    }
                }

                return response()->json([
                    'success' => true,
                    'shipping_cost_in_euro' => $shipping_cost,
                    'shipping_cost' => $shipping_cost > 0 ? MyHelpers::fromEuroView(session('currency_id', 0), $shipping_cost) : 'Shipping Cost not avilable for your sellected location',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'product_variation' => null
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'product_variation' => $th->getMessage()
            ]);
        }
    }
}
