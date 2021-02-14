<?php

use App\Facades\CurrentCustomer;
use App\Http\Controllers\LanguageSelectController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Livewire\Backend\CustomerDetailPage;
use App\Http\Livewire\Backend\CustomerListPage;
use App\Http\Livewire\Backend\CustomerManagePage;
use App\Http\Livewire\Backend\DashboardPage;
use App\Http\Livewire\Backend\DataImportExportPage;
use App\Http\Livewire\Backend\OrderDetailPage;
use App\Http\Livewire\Backend\OrderListPage;
use App\Http\Livewire\Backend\ProductManagePage;
use App\Http\Livewire\Backend\ProductListPage;
use App\Http\Livewire\Backend\SettingsPage;
use App\Http\Livewire\Backend\TextBlockEditPage;
use App\Http\Livewire\Backend\TextBlockListPage;
use App\Http\Livewire\Backend\UserEditPage;
use App\Http\Livewire\Backend\UserListPage;
use App\Http\Livewire\Backend\UserProfilePage;
use App\Http\Livewire\CheckoutPage;
use App\Http\Livewire\CustomerAccountPage;
use App\Http\Livewire\CustomerLoginPage;
use App\Http\Livewire\OrderLookupPage;
use App\Http\Livewire\ShopFrontPage;
use App\Http\Livewire\WelcomePage;
use Illuminate\Support\Facades\App;
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
        Route::get('/', WelcomePage::class)
            ->name('home');
        Route::view('privacy-policy', 'privacy-policy')
            ->name('privacy-policy');
        Route::get('customer/login', CustomerLoginPage::class)
            ->name('customer.login');
        Route::middleware('auth-customer')
            ->group(function () {
                Route::get('shop', ShopFrontPage::class)
                    ->name('shop-front');
                Route::get('checkout', CheckoutPage::class)
                    ->name('checkout');
                Route::get('order-lookup', OrderLookupPage::class)
                    ->name('order-lookup');
                Route::get('customer/account', CustomerAccountPage::class)
                    ->name('customer.account');
                Route::get('customer/logout', function () {
                    CurrentCustomer::forget();
                    return redirect()->route('home');
                })
                    ->name('customer.logout');
            });
    });

Route::get('languages', [LanguageSelectController::class, 'index'])
    ->name('languages');
Route::get('languages/{lang}', [LanguageSelectController::class, 'change'])
    ->name('languages.change');

Route::prefix('backend')
    ->name('backend.')
    ->group(function () {
        Route::get('login', [LoginController::class, 'login'])
            ->name('login')
            ->middleware('guest');
        Route::get('login/google', [SocialLoginController::class, 'redirectToGoogle'])
            ->name('login.google');
        Route::get('login/google/callback', [SocialLoginController::class, 'processGoogleCallback'])
            ->name('login.google.callback');
        Route::post('logout', [LoginController::class, 'logout'])
            ->name('logout');
});

Route::middleware('auth')
    ->group(function () {
        Route::redirect('backend', 'backend')
            ->name('backend');
        Route::prefix('backend')
            ->name('backend.')
            ->group(function () {
                Route::get('', DashboardPage::class)
                    ->name('dashboard');
                Route::get('orders', OrderListPage::class)
                    ->name('orders');
                Route::get('orders/{order}', OrderDetailPage::class)
                    ->name('orders.show');
                Route::get('customers', CustomerListPage::class)
                    ->name('customers');
                Route::get('customers/_create', CustomerManagePage::class)
                    ->name('customers.create');
                Route::get('customers/{customer}', CustomerDetailPage::class)
                    ->name('customers.show');
                Route::get('customers/{customer}/edit', CustomerManagePage::class)
                    ->name('customers.edit');
                Route::get('products', ProductListPage::class)
                    ->name('products');
                Route::get('products/_create', ProductManagePage::class)
                    ->name('products.create');
                Route::get('products/{product}/edit', ProductManagePage::class)
                    ->name('products.edit');
                Route::get('import-export', DataImportExportPage::class)
                    ->name('import-export');
                Route::get('text-blocks', TextBlockListPage::class)
                    ->name('text-blocks');
                Route::get('text-blocks/{textBlock}/edit', TextBlockEditPage::class)
                    ->name('text-blocks.edit');
                Route::get('settings', SettingsPage::class)
                    ->name('settings');
                Route::get('users', UserListPage::class)
                    ->name('users');
                Route::get('users/{user}/edit', UserEditPage::class)
                    ->name('users.edit');
                Route::get('user-profile', UserProfilePage::class)
                    ->name('user-profile');
            });
    });

if (App::environment() == 'local') {
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
}
