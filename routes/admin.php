<?php

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\VendorPayoutController;
use App\Models\User;
use App\Models\VendorShop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'auth.role:admin'])
    ->prefix('admin')
    ->name('admin-')
    ->controller(AdminController::class)->group(function () {

        // vendors
        Route::view(
            'vendors',
            'backend.admin.all_vendors',
            [
                'data' => VendorShop::with('user')->get()
            ]
        )->name('vendor-list');

        Route::post('activate_vendor', 'vendorActivate')->name('activate-vendor');
        Route::post('remove_vendor', 'vendorRemove')->name('vendor-remove');

        // users
        Route::get('user-list-page', 'userListPage')->name('user-list-page');
        Route::get('user-list', 'userList')->name('user-list');

        Route::get('activate_user/{user_id}', 'userActivate')->name('activate-user');
        Route::get('make_vendor/{user_id}', 'userMakeVendor')->name('make-vendor');
        Route::post('remove_user/{id}', 'userRemove')->name('user-remove');

        // product
        Route::get('product-list-page', 'productListPage')->name('product-list-page');
        Route::get('product-list', 'productList')->name('product-list');

        Route::get('activate_product/{product_id}', 'productActivate')->name('activate-product');
        Route::post('modify-product/{product_id}', 'modifyProduct')->name('modify-product');

        Route::get('country-list', 'country')->name('country-list');
        Route::get('country-data', 'allCountriesData')->name('country-data');
        Route::get('country-details/{id}', 'countryDetails')->name('country-details');
        Route::get('city-list/{country_id}', 'cityList')->name('city-list');
        Route::post('shipping-cost/{id}', 'saveCost')->name('shipping-cost');
        Route::get('city-cost/{id}', 'cityCost')->name('city-cost');

        //orders
        Route::get('allOrders', 'allOrders')->name('allOrders');
        Route::get('allOrdersData', 'allOrdersData')->name('allOrdersData');
        Route::get('showAllOrders/{order_item_id}', 'showAllOrders')->name('showAllOrders');

        //payments
        Route::get('all-payments', 'allPayments')->name('all-payments');

        Route::get('add-today-deal-product/{product_id}', 'addDealProducts')->name('addDealProducts');






        // fallback
        Route::fallback(function () {
            return redirect('/admin/dashboard');
        })->name('brand-fallback');
    });

Route::middleware(['auth', 'auth.role:admin'])
    ->prefix('payout')
    ->name('payout-')
    ->controller(VendorPayoutController::class)->group(function () {

        Route::post('update/{id}', 'vendorRequestUpdate')->name('update');
        Route::get('list', 'vendorRequestsAdmin')->name('list');
        Route::get('list-data', 'vendorRequestsAdminList')->name('list-data');
    });



// Route::get('/add-currency',function(){
//     return view('backend.admin.currency.index');
// })->name('add-currency');
// Route::get('add-currency', [Curre:class])->name('list');
Route::get('add-currency', [CurrencyController::class, 'index'])->name('add-currency');


Route::delete('/currencies/{id}', [CurrencyController::class, 'destroy']);

Route::get('delete_currency', [CurrencyController::class, 'destroy'])->name('delete_currency');
Route::post('/currencies/update', [CurrencyController::class, 'update']);



Route::post('store-curency', [CurrencyController::class, 'store'])->name('store_currency');
