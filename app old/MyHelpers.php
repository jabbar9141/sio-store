<?php

namespace App;

use App\Models\Currency;
use App\Models\Location;
use App\Models\product\ProductModel;
use App\Models\ShopOrderItem;
use App\Models\VendorPayout;
use App\Models\Wishlist;
use Carbon\Carbon;
use CurlHandle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MyHelpers
{

    /**
     * @param string $originalName
     * @return string
     */
    public static function encryptFileName(string $originalName): string
    {
        return substr(md5(time() . $originalName), 0, 80);
    }

    /**
     * @param $file
     * @param string $path
     * @return string
     */
    public static function uploadFile($file, string $path): string
    {
        // encrypt the file name
        $extension = $file->getClientOriginalExtension();
        $encryptedName = self::encryptFileName($file->getClientOriginalName() . time() . rand(1, 9));
        $fileName = $encryptedName . '.' . $extension;
        $file->move($path, $fileName);
        $destinationFolder = str_replace('siostore/siostore/public/', 'siostore/public_html/', $path);
        $destinationFileName = $destinationFolder . '/' . $fileName;
        copy($path . '/' . $fileName, $destinationFileName);
        return $fileName;
    }

    /**
     * @param $image
     * @param string $relativePath
     * @return string
     */
    public static function uploadImage($image, string $relativePath): string
    {
        return MyHelpers::uploadFile($image, public_path($relativePath));
    }

    /**
     * @param string $imageName
     * @param string $relativePath
     * @return void
     */
    public static function deleteImageFromStorage(string $imageName, string $relativePath)
    {
        // delete image from the uploaded images file
        $image = public_path($relativePath) . $imageName;
        try {
            unlink($image);
        } catch (\Exception $exception) {
            // log that exception
        }
    }

    /**
     * @param string $timestamp
     * @return string
     */
    public static function getDiffOfDate(string $timestamp): string
    {
        $result = Carbon::parse($timestamp)->diffForHumans();
        return $result;
    }

    /**
     * get a product by id
     *
     * @param $product_id
     * @return App\Models\product\ProductModel
     */
    public static function getProductById($product_id)
    {
        $t = ProductModel::where('product_id', $product_id)->first();
        return $t;
    }

    public static function getAllLocations()
    {
        return Location::all();
    }

    public static function userLikesItem($product_id)
    {
        $c = Wishlist::where('item_id', $product_id)->where('user_id', Auth::id())->first();
        if ($c) {
            return true;
        } else {
            return false;
        }
    }

    public static function userLikesItemCount()
    {
        $c = Wishlist::where('user_id', Auth::id())->count();
        return $c;
    }

    public static function vendorIncomeStats($vendor_id, $vendor_user_id)
    {
        $total_revenue = ShopOrderItem::whereHas('item', function ($query) use ($vendor_id) {
            $query->where('vendor_id', $vendor_id);
        })->sum('price');

        $cash_out = VendorPayout::where('user_id', $vendor_user_id)->where('status', 'Approved')->sum('response_amount');
        $cash_out_pending = VendorPayout::where('user_id', $vendor_user_id)->where('status', 'Pending')->sum('requested_amount');

        return ['total_revenue' => $total_revenue, 'total_cash_out' => $cash_out, 'cash_out_pending' => $cash_out_pending];
    }

    // devs

    // @getPrice
    public static function getPrice($price)
    {
        $currency = session()->get('session_currency') ?? 'EUR';
        if ($currency == 'EUR') {
            return $price;
        } elseif ($currency == 'USD' || $currency == 'GBP') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.freecurrencyapi.com/v1/latest?apikey=" . env('EXCHANGE_RATE_KEY') . "&currencies=" . $currency . "&base_currency=EUR");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            }
            curl_close($ch);
            $getPriceRate = json_decode($response, true);

            // return round(($getPriceRate['data'][$currency] * $price), 2);
            return $price;
        } else {
            return $price;
        }
    }

    public static function fromEuro($currency_id, $price)
    {
        if ($currency_id > 0) {
            $currency = Currency::find($currency_id);

            $result = $price * $currency->currency_rate;
            return $result;
        } else {
            return $price;
        }
    }


    public static function toEuro($currency_id, $price)
    {
        if ($currency_id > 0) {
            $currency = Currency::find($currency_id);
            $result = $price / $currency->currency_rate;
            return $result;
        } else {
            return $price;
        }
    }

    public static function fromEuroView($currency_id, $price)
    {
        if ($currency_id > 0) {
            $currency = Currency::find($currency_id);
            $result = number_format($price * $currency->currency_rate,2);
            return $result . ' ' . $currency->currency_symbol;
        } else {
            return $price . ' ' . "€";
        }
    }
    // devs

}
