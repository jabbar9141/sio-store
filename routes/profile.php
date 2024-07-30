<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ShopOrderController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\VendorController;
use App\Models\Address;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

// for admin
Route::middleware(['auth', 'auth.role:admin'])
    ->prefix('admin')
    ->name('admin-')
    ->controller(AdminController::class)->group(function () {
        // profile
        Route::view('profile', 'backend.profile.admin_profile')->name('profile');
        Route::post('profile/update_info', 'updateInfo')->name('profile-info-update');
        Route::post('profile/update_image', 'updateImage')->name('profile-image-update');
        Route::post('profile/update_password', 'updatePassword')->name('profile-password-update');
    });

// for vendor
Route::middleware(['auth', 'auth.role:vendor'])
    ->prefix('vendor')
    ->name('vendor-')
    ->controller(VendorController::class)->group(function () {

        // profile
        Route::view('profile', 'backend.profile.vendor_profile')->name('profile');
        Route::post('profile/update_info', 'updateInfo')->name('profile-info-update');
        Route::post('profile/update_image', 'updateImage')->name('profile-image-update');
        Route::post('profile/update_password', 'updatePassword')->name('profile-password-update');

        //orders
        Route::get('vendorOrders', 'vendorOrders')->name('vendorOrders');
        Route::get('vendorOrdersData', 'vendorOrdersData')->name('vendorOrdersData');
        Route::get('showVendorOrders/{order_item_id}', 'showVendorOrders')->name('showVendorOrders');
    });

// for user profile
Route::middleware(['auth', 'auth.role:user'])
    ->prefix('user')
    ->name('user-')
    ->controller(UserController::class)->group(function () {

        // profile
        Route::view('profile', 'backend.profile.user_profile')->name('profile');
        Route::post('profile/update_info', 'updateInfo')->name('profile-info-update');
        Route::post('profile/update_image', 'updateImage')->name('profile-image-update');
        Route::post('profile/update_password', 'updatePassword')->name('profile-password-update');
    });

Route::resource('user-addresses', AddressController::class)->middleware(['auth', 'auth.role:user']);
Route::resource('user-orders', ShopOrderController::class)->middleware(['auth', 'auth.role:user']);
Route::resource('user-product-review', ProductReviewController::class)->middleware(['auth', 'auth.role:user']);
