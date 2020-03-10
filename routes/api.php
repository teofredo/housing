<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
	'prefix' => 'v1',
], function() {
	
	Route::group(['prefix' => 'users'], function() {
		Route::post('/', 'UsersController@signup');	
	});
	
	Route::post('login', 'AuthController@login');
});