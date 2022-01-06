<?php

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

Route::get('/', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/cache', [\App\Http\Controllers\ProductController::class, 'pullToCache'])->name('products.cache');
Route::get('/clear', [\App\Http\Controllers\ProductController::class, 'clearCache'])->name('products.clear');
Route::post('/add-product', [\App\Http\Controllers\ProductController::class, 'addProduct'])->name('products.create');
