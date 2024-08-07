<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ShopOrderController;
use App\Http\Controllers\User\VendorController;
use App\Http\Controllers\WishlistController;
use App\Models\Announcement;
use App\Models\city;
use App\Models\Color;
use App\Models\product\ProductModel;
use App\Models\ProductVariation;
use App\Models\ShippingCost;
use App\Models\ShopOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/email', function () {
    return view('emails.email-template');
});

Route::get('/make-model', function () {
    // Generate the model and migration
    $exitCode = Artisan::call('make:migration', [
        'name' => 'add_column_quantity_in_product_variation',
    ]);

    return "Model and migration created successfully";
});

Route::get('/make-model-migration', function () {
    // Generate the model and migration
    $exitCode = Artisan::call('make:model', [
        'name' => 'Country',
        '-m' => true,
    ]);

    return "Model and migration created successfully";
});

Route::get('/product-script', function () {
    $all_products = ProductModel::get();
    foreach ($all_products as $key => $product) {
        $total_quantity = 0;
        $total_wholesale = 0;
        $total_price = 0;
        foreach ($product->variations as $key => $variation) {
            $total_quantity += $variation->product_quantity ?? 0;
            $total_price += $variation->price * $variation->product_quantity ?? 0;
            $total_wholesale += $variation->whole_sale_price * $variation->product_quantity ?? 0;
        }

        $product->update([
            'total_variation_quantity' => $total_quantity,
            'total_variation_whole_sale_price' => $total_wholesale,
            'total_variation_price' => $total_price,
        ]);
    }
});

Route::get('/add-product-columns', function () {
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2024_08_02_152948_add_columns_in_product_table.php'
    ]);
});

Route::get('/null-variations', function () {
    // Generate the model and migration
    $null_product_ids = ProductVariation::whereNull('whole_sale_price')->pluck('product_id')->toArray();

    ProductModel::whereIn('product_id', $null_product_ids)->update([
        'admin_approved' => false,
    ]);
});

// Route::get('/clear-cache', function () {
//     $exitCode = Artisan::call('cache:cache');

//     return 'Cache cleared';
// });

Route::get('/', [HomeController::class, 'index']);
Route::get('/', [HomeController::class, 'index'])->name('home-page');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
Route::post('get-in-touch', [HomeController::class, 'getInTouch'])->name('get-in-touch');
Route::post('subscriber', [HomeController::class, 'subscriber'])->name('subscriber');

Route::get('/delivery-city/{country_id}',[HomeController::class, 'getCityByCOuntryId'])->name('delivery-city');


//category
Route::get('store/category', [CategoryController::class, 'showAllCategory'])->name('store.showAllCategory');
Route::get('store/category/{slug}', [CategoryController::class, 'showCategory'])->name('store.showCategory');

//brands
Route::get('store/brand', [BrandController::class, 'showAllBrand'])->name('store.showAllBrand');
Route::get('store/brand/{slug}', [BrandController::class, 'showBrand'])->name('store.showBrand');

//vendors
Route::get('store/vendor', [App\Http\Controllers\VendorController::class, 'showAllVendor'])->name('store.showAllVendor');
Route::get('store/vendor/{slug}', [App\Http\Controllers\VendorController::class, 'showVendor'])->name('store.showVendor');


//products
Route::get('store/product/{slug?}', [ProductController::class, 'showProduct'])->name('store.showProduct');
Route::get('store/search', [ProductController::class, 'searchProducts'])->name('store.searchProducts');

//Product Reviews
Route::post('store/product/review', [ProductReviewController::class, 'storeReviewWeb'])->name('store.product.review');
Route::post('store/product/contactWithVendor', [HomeController::class, 'contactWithVendor'])->name('store.product.contactWithVendor');


//cart
Route::post('store/cart/add', [CartController::class, 'addItem'])->name('store.addItem');
Route::get('store/cart/remove', [CartController::class, 'removeItem'])->name('store.removeItem');
Route::get('store/my-cart', [CartController::class, 'my_cart'])->name('store.my-cart');

//order
Route::get('store/init/order', [ShopOrderController::class, 'initialize'])->middleware('auth')->name('store.order.init');
Route::post('store/submit/order', [ShopOrderController::class, 'submit'])->middleware('auth')->name('store.order.submit');
// Route::post('store/submit/order/{order_id}', [ShopOrderController::class, 'submit'])->middleware('auth')->name('store.order.submit');
Route::post('store/update/order/item/{item_id}', [ShopOrderController::class, 'updateItemStatus'])->middleware('auth')->name('store.order.update.item.status');
Route::post('store/create/label/item/{item_id}', [ShopOrderController::class, 'createItemShippingLabel'])->middleware('auth')->name('store.create.label.item');


Route::get('/payment-success', [ShopOrderController::class, 'payment_success'])->name('payment-success');
Route::get('/payment-error', [ShopOrderController::class, 'payment_error'])->name('payment-error');

Route::get('/payment-success-sumup', [OrderController::class, 'handleSumUpWebhook'])->name('payment_success_sumup'); //the webhook
Route::get('payment-complete-sumup/{order_id_ref}', [OrderController::class, 'payment_complete_sumup'])->name('payment_complete_sumup'); //the payment status page
Route::get('payment-complete-sumup-mobile/{order_id_ref}', [OrderController::class, 'payment_complete_sumup_mobile'])->name('payment_complete_sumup_mobile');


//address
Route::resource('address', AddressController::class)->middleware(['auth']);

Route::resource('wishlist', WishlistController::class)->middleware(['auth']);

Route::resource('announce', AnnouncementController::class)->middleware(['auth', 'auth.role:admin']);


Route::get('/set/ship/loc/{loc_id}', [LocationController::class, 'set_ship_loc'])->name('set.ship.loc');
Route::get('/search-locations', [LocationController::class, 'search_locations'])->name('search-locations');
Route::get('/estimate-order-ship-cost', [ShopOrderController::class, 'estimate_order_ship_cost'])->name('estimate-order-ship-cost');

//Pages
Route::get('/about', [HomeController::class, 'about']);
Route::get('/faq', [HomeController::class, 'faq']);
Route::get('/help', [HomeController::class, 'help']);
Route::get('/contact', [HomeController::class, 'contact']);

// KOffApIfeed


// Route::fallback(function () {
//     return redirect()->route('login');
// });

// devs

Route::get('post-currency/{currency}', [HomeController::class, 'postCurrency']);

Route::get('/term-condition', function () {

    return view('term');
});

Route::get('/privacy-policy', function () {

    return view('privacy');
});
Route::get('/disclaimer', function () {

    return view('disclaimer');
});
Route::get('/cookies', function () {

    return view('cookies');
});
Route::get('/refund-policy', function () {

    return view('refund');
});
Route::get('/licence', function () {

    return view('licence');
});

Route::get('create/payment', [ShopOrderController::class, 'createPayment'])->name('create.payment');
Route::get('cancel/payment/{order_id}', [ShopOrderController::class, 'cancel'])->name('cancel.payment');
Route::get('success/payment/{order_id}', [ShopOrderController::class, 'success'])->name('success.payment');
Route::get('success/paystack-payment/{order_id}', [ShopOrderController::class, 'successPaystack'])->name('success.paystack-payment');

Route::get('/paystack/callback/{order_id}', [ShopOrderController::class, 'payStackCallback'])->name('paystack.callback');

Route::post('city-address', function (Request $request) {
    $cities = App\Models\city::where('name', 'like', $request->input)->get();
    return response()->json([
        'cities' => $cities ?? []
    ]);
})->name('city-address');

// Route::get('/import-test', function () {
//     ShippingCost::truncate();
//     set_time_limit(10000);

//     $filePath = public_path('shipping_costs.csv');

//     $file = public_path('shipping_costs.csv');

//     // Open the file for reading
//     if (($handle = fopen($file, 'r')) !== false) {
//         // Get the first row, which contains the column headers
//         $header = fgetcsv($handle, 50000, ';');
//         // dump($header);
//         $csvData = [];

//         while (($row = fgetcsv($handle, 50000, ';')) !== false) {
//             // dd($row);

//             $csvData[] = array_combine($header, $row);
//         }

//         fclose($handle);
//     }

//     foreach ($csvData as $key => $csvs) {
//         foreach ($csvs as $c_key => $value) {
//             if ($c_key == 'Weight') {
//                 continue;
//             }
//             ShippingCost::create([
//                 'weight' => $csvs['Weight'],
//                 'country_name' => $c_key,
//                 'country_iso_2' => substr($c_key, 0, 2),
//                 'cost' => $value,
//             ]);
//         }
//     }

//     return $csvData;
// });

// devs

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/admin.php';
require_once __DIR__ . '/vendor.php';
require_once __DIR__ . '/profile.php';
require_once __DIR__ . '/user.php';
require_once __DIR__ . '/brand.php';
require_once __DIR__ . '/category.php';
require_once __DIR__ . '/sub_category.php';
require_once __DIR__ . '/product.php';
require_once __DIR__ . '/coupon.php';
require_once __DIR__ . '/notifications.php';
require_once __DIR__ . '/socialite.php';
