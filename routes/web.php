<?php

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

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return redirect('loginForm');
});

Route::get('locale/{locale}', function ($locale){
   Session::put('locale', $locale);
   return redirect()->back();
});

Route::get('loginForm', 'JwtAuth\LoginController@showLoginForm')->name('loginForm');

Route::get('registerForm', 'JwtAuth\RegisterController@showRegisterForm')->name('registerForm');

Route::middleware('guest')
    ->get('verificationCode/{code}', 'JwtAuth\RegisterController@verify')
    ->name('verification-account');





