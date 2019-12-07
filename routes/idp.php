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


Route::prefix('v1')->group(function () {

    Route::middleware(['api', 'authenticated'])->get('user', 'JwtAuth\LoginController@userByToken');
    Route::middleware(['api', 'authenticated'])->get('loginWithToken', 'JwtAuth\LoginController@userByToken');  // TODO da cancellare dopo allineamento
    Route::middleware(['api', 'authenticated'])->get('logout', 'JwtAuth\LoginController@logout');

    Route::middleware('web')->get('roles/{id}/user-roles', 'Manage\UserRoleController@getRoles')->where(['id' => '[0-9]+']);
    Route::middleware('web')->get('client-roles', 'ClientRoleController@all');
    Route::post('complete-registration', 'JwtAuth\VerificationController@verify');

    // Routes to manage users
    Route::middleware(['client', 'checkclientrole:manager'])->group(function () {

        Route::post('user', 'Manage\UserController@create');

        Route::post('users/{id}/user-roles', 'Manage\UserRoleController@create')->where(['id' => '[0-9]+']);

        Route::delete('user-role/{id}', 'Manage\UserRoleController@delete')->where(['id' => '[0-9]+']);

        Route::get('user/available/{username}', 'Manage\UserController@availableUsername');
    });

    // Routes to manage idp
    Route::middleware(['client', 'checkclientrole:admin'])->group(function () {

        Route::post('providers', 'Manage\ProviderController@create');

        Route::post('roles', 'Manage\RoleController@create');

        Route::delete('roles/{id}', 'Manage\RoleController@delete')->where(['id' => '[0-9]+']);
    });

    Route::middleware('client')->group(function () {

        Route::get('roles', 'Manage\RoleController@all');

        Route::get('employees', 'Manage\UserController@employees');

        Route::get('providers', 'Manage\ProviderController@all');

        Route::get('users/{id}', 'Manage\UserController@find')->where('id', '[0-9]+');

        Route::get('users/{id}/user-roles', 'Manage\UserRoleController@getUserRole')->where(['id' => '[0-9]+']);
    });
});

Route::prefix('v2')->group(function () {

    Route::middleware('web')->post('login', 'JwtAuth\LoginController@login')->name('login');
});
