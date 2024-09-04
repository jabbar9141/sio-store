<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductReviewsController;
use App\Http\Controllers\API\ShippingFee;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\WishlistController;
use App\Http\Controllers\CurrencyController;
use App\MyHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('test', function () {

//     $url = 'https://shop.koff.ro/feed/csv';
//     //   $response = Http::get($url);
//     //   return $response;
//     $client = new Client();
//     $response = $client->get($url, ['headers' => ['Authorization' => 'Bearer phQlvOmyJ5DgG97yfRvsydAhEemSOjWj_-QXAVns_L1xMVM1VzQKW2e2D_Kxc9kG']]);

//     if ($response->getStatusCode() == 200) {
//         $response = json_decode($response->getBody(), true);
//     return $response;

//     }
// });
// Route::post('test', [ControllersProductController::class, 'sioBuyProducts']);
Route::get('test', function () {
    $whole_sale_price = "454";
    return MyHelpers::toEuro(4, $whole_sale_price);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('profile', 'profile');
    Route::post('password-reset-request',  'requestPasswordReset');
    Route::post('password-reset-otp', 'passwordResetOTP');
    Route::post('password-reset-change', 'passwordResetChange');
});

Route::controller(ProductController::class)->prefix('products')->group(function () {
    Route::get('index', 'index');
    Route::get('show/{id}', 'show');
    Route::get('similar/{id}', 'similar');
    Route::get('featured', 'featured');
    Route::get('recent', 'recent');
    Route::get('announcements', 'announcements');
    Route::post('getVariationDetails', 'getVariationDetails');
});

Route::controller(CartController::class)->prefix('cart')->group(function () {
    Route::post('addItem', 'addItem');
    Route::post('removeItem', 'removeItem');
    Route::get('my_cart', 'my_cart');
});

Route::controller(ShippingFee::class)->prefix('shipping')->group(function () {
    Route::get('estimateItemShippingCost', 'estimateItemShippingCost');
    Route::get('estimateItemShippingCostByCity', 'estimateItemShippingCostByCity');
    Route::post('getShippingCost', 'getShippingCost');

});


Route::controller(LocationController::class)->prefix('location')->group(function () {
    Route::get('index', 'index');
    Route::get('show/{id}', 'show');
});

Route::controller(BrandController::class)->prefix('brand')->group(function () {
    Route::get('index', 'index');
    Route::get('show/{id}', 'show');
    Route::get('brandProducts/{id}', 'brandProducts');
});

Route::controller(VendorController::class)->prefix('vendor')->group(function () {
    Route::get('index', 'index');
    Route::get('show/{id}', 'show');
    Route::get('vendorProducts/{id}', 'vendorProducts');
});

Route::controller(CategoryController::class)->prefix('category')->group(function () {
    Route::get('index', 'index');
    Route::get('show/{id}', 'show');
    Route::get('categoryProducts/{id}', 'categoryProducts');
});

Route::controller(AddressController::class)->middleware(['auth:api'])->prefix('address')->group(function () {
    Route::get('index', 'index');
    Route::put('update/{id}', 'update');
    Route::post('store', 'store');
    Route::delete('delete/{id}', 'destroy');
});

Route::controller(OrderController::class)->middleware(['auth:api'])->prefix('order')->group(function () {
    Route::get('index', 'index');
    Route::get('show/{order_id}', 'show');
    Route::post('init', 'initialize');
    Route::post('submit', 'submit');
    Route::get('cancel/payment/{cart_id}', 'cancel');
    Route::get('success/payment', 'success');
});

Route::controller(WishlistController::class)->middleware(['auth:api'])->prefix('wishlist')->group(function () {
    Route::get('index', 'myWishlist');
    Route::post('addItem', 'addItem');
    Route::post('removeItem', 'removeItem');
});

Route::controller(ProductReviewsController::class)->prefix('reviews')->group(function () {
    Route::get('my-reviews', 'myReviews');
    Route::post('post-review', 'store');
    Route::get('product-reviews/{product_id}', 'getProductReviews');

    Route::put('update-review/{review_id}', 'update');
    Route::delete('delete-review/{review_id}', 'destroy');
});

Route::controller(ProductReviewsController::class)->prefix('reviews')->group(function () {
    Route::get('product-review/{product_id}', 'productReviews');
});

Route::get('currencies',[CurrencyController::class, 'getAllCurrencies']);
Route::get('countries',[CurrencyController::class, 'getAllCountries']);
Route::get('city/{country_id}',[CurrencyController::class, 'getAllCitiesOfCountry']);

