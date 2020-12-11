<?php

use App\Http\Livewire\Backend\OrderDetail;
use App\Http\Livewire\Backend\OrderList;
use App\Http\Livewire\Backend\ProductCreate;
use App\Http\Livewire\Backend\ProductEdit;
use App\Http\Livewire\Backend\ProductList;
use App\Http\Livewire\Backend\SettingsPage;
use App\Http\Livewire\CheckoutPage;
use App\Http\Livewire\OrderLookupPage;
use App\Http\Livewire\ShopFrontPage;
use Illuminate\Support\Facades\Route;

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

Route::middleware('geoblock.whitelist')
    ->group(function () {
        Route::redirect('/', 'shop')
            ->name('home');
        Route::get('shop', ShopFrontPage::class)
            ->name('shop-front');
        Route::get('checkout', CheckoutPage::class)
            ->name('checkout');
        Route::get('order-lookup', OrderLookupPage::class)
            ->name('order-lookup');
    });

Route::middleware('auth.basic')
    ->group(function () {
        Route::redirect('backend', 'backend/orders')
            ->name('backend');
        Route::prefix('backend')
            ->name('backend.')
            ->group(function () {
                Route::get('orders', OrderList::class)
                    ->name('orders');
                Route::get('orders/{order}', OrderDetail::class)
                    ->name('orders.show');
                Route::get('products', ProductList::class)
                    ->name('products');
                Route::get('products/_create', ProductCreate::class)
                    ->name('products.create');
                Route::get('products/{product}/edit', ProductEdit::class)
                    ->name('products.edit');
                Route::get('settings', SettingsPage::class)
                    ->name('settings');
            });
    });
