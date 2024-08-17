<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\product\ProductModel;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
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
            $query = ProductModel::with(['images', 'sub_category', 'category', 'brand', 'vendor',])
                ->where('product_status', 1)->where('admin_approved', 1)->withWhereHas('variations',function($query){
                    $query->where('product_quantity','>',0);
                })
                ->withCount('reviews')
                ->withAvg('reviews', 'rating');

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
                    $query->where('product_name', 'like', '%' . $searchKeyword . '%')
                        ->orWhere('product_short_description', 'like', '%' . $searchKeyword . '%')
                        ->orWhere('product_long_description', 'like', '%' . $searchKeyword . '%');
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
            Log::error('Error fetching products: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'error' => 'An error occurred while fetching products.'
            ], 500);
        }
    }

    /**
     * Display a single  resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $product
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $product_id)
    {
        try {
            $p = ProductModel::with(['images', 'sub_category', 'category', 'brand', 'vendor', 'reviews.user','variations'])->withAvg('reviews', 'rating')
                ->where('product_status', 1)->where('admin_approved', 1)->withWhereHas('variations',function($query){
                    $query->where('product_quantity','>',0);
                })
                ->withCount('reviews')
                ->addSelect([
                    'reviews_avg' => ProductReview::select(DB::raw('AVG(rating)'))
                        ->whereColumn('product_id', 'product.product_id')
                        ->limit(1)
                ])
                ->find($product_id);


            return response()->json([
                'data' => $p
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching products: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'error' => 'An error occurred while fetching products.'
            ], 500);
        }
    }

    /**
     * Get similar products
     */
    public function similar(Request $request, $product_id)
    {

        // Define pagination parameters
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $product = ProductModel::find($product_id);
        $similar = ProductModel::with(['images', 'sub_category', 'category', 'brand', 'vendor', 'reviews.user'])->withAvg('reviews', 'rating')
            ->where('product_status', 1)->where('admin_approved', 1)->withWhereHas('variations',function($query){
                $query->where('product_quantity','>',0);
            })
            ->where(function ($query) use ($product) {
                $query->where('product_name', 'like', '%' . $product->product_name . '%')
                    ->orWhere('product_short_description', 'like', '%' . $product->product_short_description . '%')
                    ->orWhere('product_long_description', 'like', '%' . $product->product_long_description . '%')
                    ->orWhere('category_id', $product->category_id);
            })
            ->where('product_id', '!=', $product->product_id)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $similar->items(),
            'current_page' => $similar->currentPage(),
            'per_page' => $similar->perPage(),
            'total' => $similar->total(),
            'last_page' => $similar->lastPage(),
            'next_page_url' => $similar->nextPageUrl(),
            'previous_page_url' => $similar->previousPageUrl()
        ]);
    }

    /**
     * Get recent products
     */
    public function recent(Request $request)
    {

        // Define pagination parameters
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $similar = ProductModel::with(['images', 'sub_category', 'category', 'brand', 'vendor', 'reviews.user'])->withAvg('reviews', 'rating')
            ->where('product_status', 1)->where('admin_approved', 1)->withWhereHas('variations',function($query){
                $query->where('product_quantity','>',0);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, ['*'], 'page', $page);


        return response()->json([
            'data' => $similar->items(),
            'current_page' => $similar->currentPage(),
            'per_page' => $similar->perPage(),
            'total' => $similar->total(),
            'last_page' => $similar->lastPage(),
            'next_page_url' => $similar->nextPageUrl(),
            'previous_page_url' => $similar->previousPageUrl()
        ]);
    }

    public function announcements()
    {
        $ann = Announcement::where('status', 1)->paginate(20);

        return response()->json([
            'data' => $ann->items(),
            'current_page' => $ann->currentPage(),
            'per_page' => $ann->perPage(),
            'total' => $ann->total(),
            'last_page' => $ann->lastPage(),
            'next_page_url' => $ann->nextPageUrl(),
            'previous_page_url' => $ann->previousPageUrl()
        ]);
    }
}
