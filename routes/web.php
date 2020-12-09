<?php

use App\Http\Livewire\Backend\OrderDetail;
use App\Http\Livewire\Backend\OrderList;
use App\Http\Livewire\Backend\ProductCreate;
use App\Http\Livewire\Backend\ProductEdit;
use App\Http\Livewire\Backend\ProductList;
use App\Http\Livewire\CheckoutPage;
use App\Http\Livewire\WelcomePage;
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

Route::get('/', WelcomePage::class)
    ->name('welcome');
Route::get('checkout', CheckoutPage::class)
    ->name('checkout');

Route::redirect('backend', 'backend/orders')
    ->name('backend');
Route::prefix('backend')
    ->name('backend.')
    ->middleware('auth.basic')
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
    });
