<?php

use App\Http\Controllers\LanguageSelectController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Livewire\AboutPage;
use App\Http\Livewire\Backend\BlockedPhoneNumbersPage;
use App\Http\Livewire\Backend\CustomerDetailPage;
use App\Http\Livewire\Backend\CustomerListPage;
use App\Http\Livewire\Backend\CustomerManagePage;
use App\Http\Livewire\Backend\DashboardPage;
use App\Http\Livewire\Backend\DataExportPage;
use App\Http\Livewire\Backend\OrderDetailPage;
use App\Http\Livewire\Backend\OrderListPage;
use App\Http\Livewire\Backend\ProductManagePage;
use App\Http\Livewire\Backend\ProductListPage;
use App\Http\Livewire\Backend\ReportsPage;
use App\Http\Livewire\Backend\SettingsPage;
use App\Http\Livewire\Backend\StockEditPage;
use App\Http\Livewire\Backend\StockPage;
use App\Http\Livewire\Backend\TextBlockEditPage;
use App\Http\Livewire\Backend\TextBlockListPage;
use App\Http\Livewire\Backend\UserEditPage;
use App\Http\Livewire\Backend\UserListPage;
use App\Http\Livewire\Backend\UserProfilePage;
use App\Http\Livewire\CheckoutPage;
use App\Http\Livewire\CustomerAccountPage;
use App\Http\Livewire\CustomerLoginPage;
use App\Http\Livewire\CustomerRegistrationPage;
use App\Http\Livewire\MyOrdersPage;
use App\Http\Livewire\ShopFrontPage;
use App\Models\BlockedPhoneNumber;
use App\Models\TextBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        Route::view('privacy-policy', 'privacy-policy')
            ->name('privacy-policy');
        Route::get('about', AboutPage::class)
            ->name('about');
        Route::get('shop', ShopFrontPage::class)
            ->name('shop-front')
            ->middleware('customer-disabled-check');
        Route::middleware('guest:customer')
            ->group(function () {
                Route::get('customer/login', CustomerLoginPage::class)
                    ->name('customer.login');
                Route::get('customer/registration', CustomerRegistrationPage::class)
                    ->name('customer.registration');
            });
        Route::middleware(['auth:customer', 'customer-disabled-check'])
            ->group(function () {
                Route::get('checkout', CheckoutPage::class)
                    ->name('checkout');
                Route::get('my-orders', MyOrdersPage::class)
                    ->name('my-orders');
                Route::redirect('order-lookup', 'my-orders');
                Route::get('customer/account', CustomerAccountPage::class)
                    ->name('customer.account');
                Route::get('customer/logout', function (Request $request) {
                    Auth::guard('customer')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
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
        Route::redirect('backend', 'backend/dashboard')
            ->name('backend');
        Route::prefix('backend')
            ->name('backend.')
            ->group(function () {
                Route::get('dashboard', DashboardPage::class)
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
                Route::get('stock', StockPage::class)
                    ->name('stock');
                Route::get('stock/{product}', StockEditPage::class)
                    ->name('stock.edit');
                Route::get('export', DataExportPage::class)
                    ->name('export');
                Route::get('reports', ReportsPage::class)
                    ->name('reports');
                Route::get('configuration', function () {
                    if (auth()->user()->can('update settings')) {
                        return redirect()->route('backend.configuration.settings');
                    }
                    if (auth()->user()->can('viewAny', TextBlock::class)) {
                        return redirect()->route('backend.configuration.text-blocks');
                    }
                    if (auth()->user()->can('viewAny', BlockedPhoneNumber::class)) {
                        return redirect()->route('backend.configuration.blocked-phone-numbers');
                    }
                    return abort(Response::HTTP_FORBIDDEN);
                })
                    ->name('configuration');
                Route::get('configuration/settings', SettingsPage::class)
                    ->name('configuration.settings');
                Route::get('configuration/text-blocks', TextBlockListPage::class)
                    ->name('configuration.text-blocks');
                Route::get('configuration/text-blocks/{textBlock}/edit', TextBlockEditPage::class)
                    ->name('configuration.text-blocks.edit');
                Route::get('configuration/blocked-phone-numbers', BlockedPhoneNumbersPage::class)
                    ->name('configuration.blocked-phone-numbers');
                Route::get('users', UserListPage::class)
                    ->name('users');
                Route::get('users/{user}/edit', UserEditPage::class)
                    ->name('users.edit');
                Route::get('user-profile', UserProfilePage::class)
                    ->name('user-profile');
            });
    });
