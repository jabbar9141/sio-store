<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\User\AdminController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

// for admin
Route::middleware(['auth', 'auth.role:admin'])
    ->prefix('admin')
    ->name('admin-')
    ->controller(ProductController::class)->group(function () {

        Route::view(
            'products',
            'backend.product.product_default',
            ['data' => DB::table('product')->get()]
        )
            ->name('product');
        Route::get('remove_product/{id}', 'productRemove')
            ->whereNumber('id')
            ->name('product-remove');
    });

// for vendor
Route::middleware(['auth', 'auth'])
    ->prefix('vendor')
    ->name('vendor-')
    ->controller(ProductController::class)->group(function () {

        Route::get('products', 'getProducts')->name('product');
        Route::get('add_product', 'productAdd')->name('product-add');
        Route::post('create_product', 'productCreate')->name('product-create');
        Route::get('koff-api-products','koffFeedProducts')->name('koffFeedProducts');
        Route::get('updateProducts','updateProducts')->name('updateProducts');
        Route::get('updateProductsBrandCategory','updateProductsBrandCategory')->name('updateProductsBrandCategory');
   
        Route::post('bulkRemove','bulkRemove')->name('bulkRemove');
        Route::post('sioBuyProducts','sioBuyProducts')->name('sioBuyProducts');
        Route::get('product-list', 'productList')->name('product-list');
        Route::get('remove_product/{id}', 'productRemove')
            ->whereNumber('id')
            ->name('product-remove');
        Route::get('edit_product/{id}', 'productEdit')
            ->whereNumber('id')->name('product-edit');
        Route::post('update_product/{id}', 'productUpdate')
            ->whereNumber('id')->name('product-update');
        Route::post('activate_product', 'productActivate')->name('product-activate');
        Route::post('massInsertProducts', 'massInsertProducts')->name('product-massInsertProducts');
        Route::post('massInsertProductsFromCSV', 'massInsertProductsFromCSV')->name('product-massInsertProductsFromCSV');
        

        
    });

Route::post('getVariationDetails', [ProductController::class, 'getVariationDetails'])->name('getVariationDetails');
Route::get('/product/details/{id}', [ProductController::class, 'getProductDetails']);
Route::get('/admin/product/details/{id}', [AdminController::class, 'getProductDetails']);
