<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('v1')->group( function() {

    Route::middleware('api')->get('/loginWithToken', 'JwtAuth\LoginController@loginWithToken');
    Route::middleware('api')->get('/logout', 'JwtAuth\LoginController@logout');

    Route::middleware('web')->post('/login', 'JwtAuth\LoginController@login')->name('login');
    Route::middleware('web')->post('/register', 'JwtAuth\RegisterController@register')->name('register');

});

