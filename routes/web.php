<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


// Authentication
Route::post('/login', 'Auth\LoginController@login');
Route::post('/refresh_token', 'Auth\LoginController@refreshToken');
Route::post('/logout', 'Auth\LoginController@logout');
Route::post('/register', 'Auth\RegisterController@register');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');

// Every other route gets handled by Vue
Route::get('{slug}', function () {
    return view('main');
})->where('slug', '^(?!api/?).*');

