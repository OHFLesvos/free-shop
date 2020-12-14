<?php

use App\Http\Controllers\LanguageSelectController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Livewire\Backend\CustomerCreatePage;
use App\Http\Livewire\Backend\CustomerDetailPage;
use App\Http\Livewire\Backend\CustomerListPage;
use App\Http\Livewire\Backend\CustomerEditPage;
use App\Http\Livewire\Backend\DataImportExportPage;
use App\Http\Livewire\Backend\OrderDetailPage;
use App\Http\Livewire\Backend\OrderListPage;
use App\Http\Livewire\Backend\ProductCreatePage;
use App\Http\Livewire\Backend\ProductEditPage;
use App\Http\Livewire\Backend\ProductListPage;
use App\Http\Livewire\Backend\SettingsPage;
use App\Http\Livewire\Backend\UserProfilePage;
use App\Http\Livewire\CheckoutPage;
use App\Http\Livewire\OrderLookupPage;
use App\Http\Livewire\ShopFrontPage;
use Illuminate\Support\Facades\Auth;
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
        Route::get('/', function() {
                if (! session()->has('lang')) {
                    return redirect()->route('languages');
                }
                return redirect()->route('shop-front');
            })
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

Route::view('login', 'login')
    ->name('login')
    ->middleware('guest');
Route::get('login/google', [SocialLoginController::class, 'redirectToGoogle'])
    ->name('login.google');
Route::get('login/google/callback', [SocialLoginController::class, 'processGoogleCallback'])
    ->name('login.google.callback');
Route::post('logout', function() {
        return redirect('/')
            ->with(Auth::logout());
    })
    ->name('logout');

Route::middleware('auth')
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
                Route::get('customers', CustomerListPage::class)
                    ->name('customers');
                Route::get('customers/_create', CustomerCreatePage::class)
                    ->name('customers.create');
                Route::get('customers/{customer}', CustomerDetailPage::class)
                    ->name('customers.show');
                Route::get('customers/{customer}/edit', CustomerEditPage::class)
                    ->name('customers.edit');
                Route::get('products', ProductListPage::class)
                    ->name('products');
                Route::get('products/_create', ProductCreatePage::class)
                    ->name('products.create');
                Route::get('products/{product}/edit', ProductEditPage::class)
                    ->name('products.edit');
                Route::get('import-export', DataImportExportPage::class)
                    ->name('import-export');
                Route::get('settings', SettingsPage::class)
                    ->name('settings');
                Route::get('user-profile', UserProfilePage::class)
                    ->name('user-profile');
            });
    });
