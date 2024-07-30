<?php

use App\Http\Controllers\User\VendorController;
use App\Http\Controllers\VendorPayoutController;
use App\Http\Controllers\WalkInOrderController;
use App\Models\VendorPayout;
use App\Models\WalkInOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| vendor Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'auth.role:vendor'])
    ->prefix('vendor')
    ->name('vendor-')
    ->controller(VendorController::class)->group(function () {

        // profile
        Route::view('profile', 'backend.profile.vendor_profile')->name('profile');
        Route::post('profile/update_info', 'updateInfo')->name('profile-info-update');
        Route::post('profile/update_image', 'updateImage')->name('profile-image-update');
        Route::post('profile/update_password', 'updatePassword')->name('profile-password-update');

        // fallback
        Route::fallback(function () {
            return redirect('/vendor/profile');
        })->name('brand-fallback');
    });


Route::middleware(['auth', 'auth.role:vendor'])
    ->prefix('vendor-payout')
    ->name('vendor-payout-')
    ->controller(VendorPayoutController::class)->group(function () {

        Route::post('request', 'vendorRequest')->name('request');
        Route::get('list', 'vendorRequests')->name('list');
        Route::get('list-data', 'vendorRequestsList')->name('list-data');
    });

Route::middleware(['auth', 'auth.role:vendor'])
    ->prefix('walk-in-order')
    ->name('walk-in-order-')
    ->controller(WalkInOrder::class)->group(function () {
        Route::get('list', 'walkInOrders')->name('list');
        Route::get('list-data', 'walkInOrdersList')->name('list-data');
    });

Route::resource('walk-in-order', WalkInOrderController::class)->middleware(['auth', 'auth.role:vendor']);
Route::get('/walk-in-order-product-search', [WalkInOrderController::class, 'search'])->name('walk-in-order.product.search');

