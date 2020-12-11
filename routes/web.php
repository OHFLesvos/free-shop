<?php

use App\Http\Controllers\LanguageSelectController;
use App\Http\Livewire\Backend\OrderDetailPage;
use App\Http\Livewire\Backend\OrderListPage;
use App\Http\Livewire\Backend\ProductCreatePage;
use App\Http\Livewire\Backend\ProductEditPage;
use App\Http\Livewire\Backend\ProductListPage;
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

Route::middleware(['geoblock.whitelist', 'set-language'])
    ->group(function () {
        Route::redirect('/', 'shop')
            ->name('home');
        Route::get('languages', [LanguageSelectController::class, 'index'])
            ->name('languages');
        Route::get('languages/{lang}', [LanguageSelectController::class, 'change'])
            ->name('languages.change');
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
                Route::get('orders', OrderListPage::class)
                    ->name('orders');
                Route::get('orders/{order}', OrderDetailPage::class)
                    ->name('orders.show');
                Route::get('products', ProductListPage::class)
                    ->name('products');
                Route::get('products/_create', ProductCreatePage::class)
                    ->name('products.create');
                Route::get('products/{product}/edit', ProductEditPage::class)
                    ->name('products.edit');
                Route::get('settings', SettingsPage::class)
                    ->name('settings');
            });
    });
