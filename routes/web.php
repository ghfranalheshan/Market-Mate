<?php

use App\Http\Controllers\MarketController;
use App\Http\Controllers\MCategoryController;
use App\Http\Controllers\ProductAdminControler;
use Illuminate\Support\Facades\Mail;
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


Route::resource('MCategory',MCategoryController::class);

Route::resource('Product',ProductAdminControler::class);

Route::get('Market',[MarketController::class,'indexMarket'])->name('index');
Route::get('Market/{market}',[MarketController::class,'show'])->name('show');

Auth::routes();

Route::get('/',[ProductAdminControler::class,'total'])->name('Home');

Route::get('/profile',function (){
    return view('auth.profile');
})->name('profile');


Route::get('/f',function (){
    return view('home');
});
