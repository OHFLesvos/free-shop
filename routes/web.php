<?php

use App\Http\Livewire\Backend\OrderList;
use App\Http\Livewire\Backend\ProductList;
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

Route::redirect('backend', 'backend/orders')
    ->name('backend');
Route::prefix('backend')
    ->name('backend.')
    ->group(function () {
        Route::get('orders', OrderList::class)
            ->name('orders');
        Route::get('products', ProductList::class)
            ->name('products');
    });
