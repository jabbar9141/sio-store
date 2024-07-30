<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductReviewsController extends Controller
{

    public function productReviews(Request $request, $product_id)
    {
        try {
            // Define pagination parameters
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            // Start building the query to fetch products
            $query = ProductReview::with(['product', 'user'])->where('product_id', $product_id);

            // Apply filtering
            if ($request->has('filter_column') && $request->has('filter_value')) {
                $filterColumn = $request->input('filter_column');
                $filterValue = $request->input('filter_value');

                // Validate if the filter column exists in the model
                if (in_array($filterColumn, (new ProductReview())->getFillable())) {
                    $query->where($filterColumn, $filterValue);
                }
            }

            // Apply search
            if ($request->has('search_keyword')) {
                $searchKeyword = $request->input('search_keyword');
                $query->where(function ($query) use ($searchKeyword) {
                    $query->where('comment', 'like', '%' . $searchKeyword . '%');
                });
            }

            // Apply sorting
            if ($request->has('sort_by')) {
                $sortField = $request->input('sort_by');
                $sortDirection = $request->input('sort_direction', 'asc');
                $query->orderBy($sortField, $sortDirection);
            }

            // Fetch products with applied filters, search, and sorting
            $reviews = $query->paginate($perPage, ['*'], 'page', $page);

            // Return paginated JSON response
            return response()->json([
                'data' => $reviews->items(),
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
                'next_page_url' => $reviews->nextPageUrl(),
                'previous_page_url' => $reviews->previousPageUrl()
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching product reviews: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'error' => 'An error occurred while fetching product reviews.'
            ], 500);
        }
    }

    public function myReviews(Request $request)
    {
        try {
            // Define pagination parameters
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            // Start building the query to fetch
            $query = ProductReview::with(['product', 'user'])->where('user_id', auth()->guard('api')->id());

            // Apply filtering
            if ($request->has('filter_column') && $request->has('filter_value')) {
                $filterColumn = $request->input('filter_column');
                $filterValue = $request->input('filter_value');

                // Validate if the filter column exists in the model
                if (in_array($filterColumn, (new ProductReview())->getFillable())) {
                    $query->where($filterColumn, $filterValue);
                }
            }

            // Apply search
            if ($request->has('search_keyword')) {
                $searchKeyword = $request->input('search_keyword');
                $query->where(function ($query) use ($searchKeyword) {
                    $query->where('comment', 'like', '%' . $searchKeyword . '%');
                });
            }

            // Apply sorting
            if ($request->has('sort_by')) {
                $sortField = $request->input('sort_by');
                $sortDirection = $request->input('sort_direction', 'asc');
                $query->orderBy($sortField, $sortDirection);
            }

            // Fetch with applied filters, search, and sorting
            $reviews = $query->paginate($perPage, ['*'], 'page', $page);

            // Return paginated JSON response
            return response()->json([
                'data' => $reviews->items(),
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
                'next_page_url' => $reviews->nextPageUrl(),
                'previous_page_url' => $reviews->previousPageUrl()
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching user product reviews: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'error' => 'An error occurred while fetching user product reviews.'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|min:1|max:5',
            'product_id' => 'required'
        ]);

        try {
            $review = new ProductReview();
            $review->user_id = auth()->guard('api')->id();
            $review->product_id = $request->input('product_id'); // Retrieve product ID from the form
            $review->rating = $request->input('rating');
            $review->comment = $request->input('comment');
            $review->save();

            return response()->json(['message' => "Review sumitted", "data" => $review]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['error' => "Failed to submit, please try again later."], 500);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $review_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $review_id)
    {
        $request->validate([
            'rating' => 'required|min:1|max:5'
        ]);

        try {
            $review = ProductReview::where('id', $review_id)->first();
            if ($request->input('rating')) {
                $review->rating = $request->input('rating');
            }
            $review->comment = $request->input('comment');
            $review->save();

            return response()->json(['message' => "Review Updated", "data" => $review]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return response()->json(['error' => "Failed to update review, please try again later."]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $review_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($review_id)
    {
        $review = ProductReview::where('id', $review_id)->where('user_id', auth()->guard('api')->id())->first();
        try {
            // Check if the review exists
            if (!$review) {
                throw new \Exception('Review not found.');
            }

            // Delete the review
            $review->delete();

            return response()->json(['message' => 'Review deleted successfully.']);
        } catch (\Exception $e) {
            // Handle errors
            Log::error($e->getMessage(), ['exception' => $e]);

            return response()->json(['error' => 'Failed to delete review. Please try again later.']);
        }
    }
}
