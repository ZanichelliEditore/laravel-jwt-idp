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

Route::get('locale/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return redirect()->back();
});

Route::middleware('guest')
    ->get('loginForm', 'JwtAuth\LoginController@showLoginForm')
    ->name('loginForm');

Route::get('logout', 'JwtAuth\LoginController@logout')->name('logout');

Route::middleware('web.authenticated')
    ->get('authenticated', 'JwtAuth\LoginController@authenticated')
    ->name('authenticated');

Route::get('complete-registration', function () {
    return view('auth.complete-registration-form');
})->name('complete-registration');

/********* ADMIN ROUTES ************/

Route::prefix('admin')->middleware('role:ADMIN_IDP')->group(function () {

    Route::get('/', function () {
        return redirect()->route('users-panel');
    })->name('admin-board');

    Route::post('users', 'Manage\UserController@create');

    Route::get('users-panel', function () {
        return view('admin.users');
    })->name('users-panel');

    Route::post('providers', 'Manage\ProviderController@create');

    Route::get('create-provider', function () {
        return view('admin.create-provider');
    })->name('create-provider');

    Route::get('oauth-clients', function () {
        return view('admin.oauth-clients');
    })->name('oauth-clients');

    Route::get('oauth-clients-all', 'Manage\OauthClientsController@all');
    Route::put('update-roles', 'Manage\OauthClientsController@updateClientRoles');

    Route::get('roles', 'Manage\RoleController@all');
    Route::post('roles', 'Manage\RoleController@create');
    Route::delete('roles/{id}', 'Manage\RoleController@delete')->where(['id' => '[0-9]+']);

    Route::get('users', 'Manage\UserController@all');

    Route::get('manage-role', function () {
        return view('admin.create-role');
    })->name('manage-role');
});
