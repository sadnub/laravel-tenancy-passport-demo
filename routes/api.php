<?php

use Illuminate\Support\Facades\Auth;
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

//Auth Routes

Route::group(['prefix' => 'v1'], function () {

	Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
	Route::post('password/reset', 'Auth\ResetPasswordController@reset');
	Route::post('register', 'Auth\RegisterController@register');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'v1'], function () {
    
    Route::apiResource('tickets', 'API\TicketController');
});
