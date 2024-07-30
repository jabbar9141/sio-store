<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\product\ProductModel;
use App\Models\ProductReview;
use App\Models\VendorShop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Define pagination parameters
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            // Start building the query to fetch products
            $query = VendorShop::query();

            // Apply filtering
            if ($request->has('filter_column') && $request->has('filter_value')) {
                $filterColumn = $request->input('filter_column');
                $filterValue = $request->input('filter_value');

                // Validate if the filter column exists in the model
                if (in_array($filterColumn, (new VendorShop())->getFillable())) {
                    $query->where($filterColumn, $filterValue);
                }
            }

            // Apply search
            if ($request->has('search_keyword')) {
                $searchKeyword = $request->input('search_keyword');
                $query->where(function ($query) use ($searchKeyword) {
                    $query->where('shop_name', 'like', '%' . $searchKeyword . '%')
                        ->orWhere('shop_description', 'like', '%' . $searchKeyword . '%');
                });
            }

            // Apply sorting
            if ($request->has('sort_by')) {
                $sortField = $request->input('sort_by');
                $sortDirection = $request->input('sort_direction', 'asc');
                $query->orderBy($sortField, $sortDirection);
            }

            // Fetch products with applied filters, search, and sorting
            $vendors = $query->paginate($perPage, ['*'], 'page', $page);

            // Return paginated JSON response
            return response()->json([
                'data' => $vendors->items(),
                'current_page' => $vendors->currentPage(),
                'per_page' => $vendors->perPage(),
                'total' => $vendors->total(),
                'last_page' => $vendors->lastPage(),
                'next_page_url' => $vendors->nextPageUrl(),
                'previous_page_url' => $vendors->previousPageUrl()
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching vendors: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'error' => 'An error occurred while fetching vendors.'
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $vendor = VendorShop::with(['user'])->where('vendor_id', $id)->first();
            return response()->json($vendor);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching vendor: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'error' => 'An error occurred while fetching vendor.'
            ], 500);
        }
    }

    public function vendorProducts(Request $request, $id)
    {
        try {
            // Define pagination parameters
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            // Start building the query to fetch products
            $query = ProductModel::with(['images', 'sub_category', 'category', 'brand', 'vendor', 'reviews.user'])
                ->where('product_status', 1)->where('admin_approved', 1)->where('product_quantity', '>', 0)
                ->where('vendor_id', $id)
                ->withCount('reviews')
                ->addSelect([
                    'reviews_avg' => ProductReview::select(DB::raw('AVG(rating)'))
                        ->whereColumn('product_id', 'product.product_id')
                        ->limit(1)
                ]);

            // Apply filtering
            if ($request->has('filter_column') && $request->has('filter_value')) {
                $filterColumn = $request->input('filter_column');
                $filterValue = $request->input('filter_value');

                // Validate if the filter column exists in the model
                if (in_array($filterColumn, (new ProductModel())->getFillable())) {
                    $query->where($filterColumn, $filterValue);
                }
            }

            // Apply search
            if ($request->has('search_keyword')) {
                $searchKeyword = $request->input('search_keyword');
                $query->where(function ($query) use ($searchKeyword) {
                    $query->where('shop_name', 'like', '%' . $searchKeyword . '%')
                        ->orWhere('shop_description', 'like', '%' . $searchKeyword . '%');
                });
            }

            // Apply sorting
            if ($request->has('sort_by')) {
                $sortField = $request->input('sort_by');
                $sortDirection = $request->input('sort_direction', 'asc');
                $query->orderBy($sortField, $sortDirection);
            }

            // Fetch products with applied filters, search, and sorting
            $products = $query->paginate($perPage, ['*'], 'page', $page);

            // Return paginated JSON response
            return response()->json([
                'data' => $products->items(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'previous_page_url' => $products->previousPageUrl()
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching vendor products: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'error' => 'An error occurred while fetching vendor products.'
            ], 500);
        }
    }
}
