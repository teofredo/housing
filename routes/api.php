<?php

use Illuminate\Http\Request;

Route::group([
	'prefix' => 'v1',
	'middleware' => [
		'auth:api',
		// 'throttle:60,1'
	]
], function() {
	Route::get('/user', 'AuthController@user');
	Route::post('/logout', 'AuthController@logout');
});

Route::group([
	'prefix' => 'v1',
	'middleware' => [
		// 'throttle:60,1'
	]
], function() {
	Route::post('/signup', 'AuthController@signup');
	Route::post('/login', 'AuthController@login');
});