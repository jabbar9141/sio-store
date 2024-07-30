<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $request->validate([
            'rating' => 'required|min:1|max:5'
        ]);

        try {
            $review = new ProductReview();
            $review->user_id = auth()->id();
            $review->product_id = $request->input('product_id'); // Retrieve product ID from the form
            $review->rating = $request->input('rating');
            $review->comment = $request->input('comment');
            $review->save();

            return back()->with(['success' => "Review sumitted"]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with(['error' => "Failed to submit, please try again later."]);
        }
    }
    
    public function storeReviewWeb(Request $request)
    {   
    
        try {
            $review = new ProductReview();
            $review->user_id = auth()->id() ?? 0;
            $review->product_id = $request->input('product_id');
            $review->username = $request->username ?? '';
            $review->rating = $request->input('rating');
            $review->comment = $request->input('comment');
            $review->save();

            return response()->json([
                'success' => true,
                'msg' => "Review sumitted",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => "Something went wrong",
            ]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductReview  $productReview
     * @return \Illuminate\Http\Response
     */
    public function show(ProductReview $productReview)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductReview  $productReview
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductReview $productReview)
    {
        //
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

            return back()->with(['success' => "Review Updated"]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with(['error' => "Failed to update, please try again later."]);
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
        $review = ProductReview::where('id', $review_id)->first();
        try {
            // Check if the review exists
            if (!$review) {
                throw new \Exception('Review not found.');
            }

            // Delete the review
            $review->delete();

            return back()->with('success', 'Review deleted successfully.');
        } catch (\Exception $e) {
            // Handle errors
            Log::error($e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Failed to delete review. Please try again later.');
        }
    }
}
