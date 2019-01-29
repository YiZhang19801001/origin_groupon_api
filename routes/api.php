<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'api-header'], function () {
    Route::get('product/{product_id}', 'ProductController@show');
    Route::get('products/{language_id}', 'ProductController@index');
    // Route::get('products/{language_id}/{product_id}', 'ProductController@show');
    Route::post('products', 'ProductController@create');
    Route::put('products/{product_id}', 'ProductController@update');

    Route::get('categories/{language_id}', 'CategoryController@index');
    Route::get('categories/{language_id}/{category_id}', 'CategoryController@show');
    Route::post('categories', 'CategoryController@create');
    Route::put('categories/{category_id}', 'CategoryController@update');

    Route::get('locations', 'LocationController@index');
    Route::get('locations/{location_id}', 'LocationController@show');
    Route::post('locations', 'LocationController@create');
    Route::put('locations/{location_id}', 'LocationController@update');

    // The registration and login requests doesn't come with tokens
    // as users at that point have not been authenticated yet
    // Therefore the jwtMiddleware will be exclusive of them
    Route::post('user/login', 'UserController@login');
    Route::post('user/register', 'UserController@register');
});

Route::group(['middleware' => ['jwt.auth', 'api-header']], function () {

    // all routes to protected resources are registered here
    Route::get('users/list', function () {
        $users = App\User::all();

        $response = ['success' => true, 'data' => $users];
        return response()->json($response, 201);
    });
});
Route::group(['middleware' => 'api-header'], function () {

    // The registration and login requests doesn't come with tokens
    // as users at that point have not been authenticated yet
    // Therefore the jwtMiddleware will be exclusive of them
    Route::post('user/login', 'UserController@login');
    Route::post('user/register', 'UserController@register');
});
