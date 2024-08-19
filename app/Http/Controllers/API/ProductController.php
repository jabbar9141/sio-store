<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CityShippingCost;
use App\Models\Country;
use App\Models\product\ProductModel;
use App\Models\ProductReview;
use App\Models\ProductVariation;
use App\Models\ShippingCost;
use App\MyHelpers;
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

    public function getVariationDetails(Request $request)
    {   
        try {
            $country_id = $request->country_id;
            $city_id = $request->city_id;
            $currency_id = $request->currency_id;
            $product = ProductModel::find($request->product_id);
            $query = ProductVariation::query();
            $query->where('product_id', $request->product_id);
            if ($request->filled('color_name')) {
                $query->where('color_name', $request->color_name);
            }
    
            if ($request->filled('width')) {
                $query->where('width', $request->width);
            }
    
            if ($request->filled('height')) {
                $query->where('height', $request->height);
            }
    
            if ($request->filled('length')) {
                $query->where('length', $request->length);
            }
    
            if ($request->filled('weight')) {
                $query->where('weight', $request->weight);
            }
            if ($request->filled('size_name')) {
                $productVariations = $query->get();
                foreach ($productVariations as $productVariation) {
                    $sizesArray = explode(',', $productVariation->size_name);
    
                    if (in_array($request->size_name, $sizesArray)) { {
                            return response()->json([
                                'success' => true,
                                'product_variation' => $productVariation,
                                'product_images' => count(json_decode($productVariation->image_url)) > 0 ? json_decode($productVariation->image_url) : $product?->product_thumbnail,
                                'video_url' => json_decode($productVariation->video_url),
                            ]);
                        }
                    }
                }
            }
            $productVariation = $query->first();
    
            if ($productVariation) {
                $available_regions = json_decode($product->available_regions);
                if(!is_array($available_regions)){
                    $available_regions = json_decode($available_regions, true);
                }
    
                if ($product->vendor->user->currency) {
                    $vendor_country = Country::where('name', 'like', $product->vendor->user->currency->country)->first();
                } else {
                    $vendor_country = Country::where('name', 'like', 'Italy')->first();
                }
    
                if ($vendor_country->id == (int) $country_id) {
                    $city_percentage = CityShippingCost::where('city_id', (int) $city_id)->first()?->percentage;
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
                    if (in_array((int) $country_id, $countries_origins)) {
                        $shipping_cost = ShippingCost::where('country_iso_2', $vendor_country->iso2)->where('weight', $productVariation->weight)->first()?->cost;
                    } else {
                        $shipping_cost = 0;
                    }
                   
                }
    
                return response()->json([
                    'success' => true,
                    'product_variation' => $productVariation,
                    'product_images' => isset($productVariation->image_url) && count(json_decode($productVariation->image_url)) > 0 ? json_decode($productVariation->image_url) : [$product?->product_thumbnail],
                    'video_url' => json_decode($productVariation->video_url),
                    'formatedPrice' => MyHelpers::fromEuroView($currency_id, $productVariation->price),
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
            ],500);
        }
      
    }
}
