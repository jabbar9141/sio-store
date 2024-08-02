<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BrandModel;
use App\Models\Currency;
use App\Models\product\ProductModel;
use App\Models\VendorShop;
use App\MyHelpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function showAllVendor(Request $request)
    {
        $vendors = VendorShop::with(['user'])->paginate(20);
        return view('user.all_vendors', ['vendors' => $vendors]);
    }

    public function showVendor(Request $request, $id)
    {
        $vendor = VendorShop::with(['user'])->where('vendor_id', $id)->first();
        if ($vendor) {
            $products = ProductModel::with(['category', 'brand'])->where('product_status', 1)->where('admin_approved', 1)->where('product_quantity', '>', 0)->where('vendor_id', $vendor->vendor_id)->paginate(50);
            return view('user.vendor', ['vendor' => $vendor, 'products' => $products]);
        } else {
            abort(404, 'Vendor Not Found');
        }
    }
}
