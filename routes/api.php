<?php

use App\UserDevice;
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

Route::group([
    'middleware' => 'api'
  ], function() {
    Route::prefix('user')->group(function () {
        Route::get('getAll', 'UserController@getAll');
    });
    Route::resource('user', UserController::class);
    Route::prefix('customers')->group(function () {
        Route::get('getAll', 'CustomerController@getAll');
        Route::get('/check-mailchimp','CustomerController@getCustomersNotInListMailChimp');
    });
    Route::resource('customers', CustomerController::class);
    Route::resource('quotes', QuoteController::class);
    // Route::resource('logs', LogsController::class);
    Route::prefix('logs')->group(function () {
        Route::get('/{id}', 'LogController@getBitacoraByQuote');
    });
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signUp');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('user', 'AuthController@user');
        Route::get('roles', 'UserController@getRoles');
        Route::put('roles', 'UserController@updatedRoles');
        Route::post('roles', 'UserController@storeRoles');
        Route::delete('roles/{key}', 'UserController@deleteRole');
        Route::get('logout', 'AuthController@logout');
   });
});