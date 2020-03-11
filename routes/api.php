<?php

use Illuminate\Http\Request;

Route::group([
	'prefix' => 'v1',
	'middleware' => 'auth:api'
], function() {
	Route::get('/user', 'AuthController@user');
});

Route::group([
	'prefix' => 'v1',
], function() {
	
	Route::group(['prefix' => 'users'], function() {
		Route::post('/', 'UsersController@signup');	
	});
	
	Route::post('login', 'AuthController@login');
});