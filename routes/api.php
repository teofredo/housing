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

//blocks
Route::group([
	'prefix' => 'v1/blocks',
	'middleware' => [
		'auth:api',
		// 'throttle:60,1'
	]
], function() {
	Route::get('/{id?}', 'BlocksController@index');
	Route::post('/', 'BlocksController@post');
});

//lots
Route::group([
	'prefix' => 'v1/lots',
	'middleware' => [
		'auth:api',
		// 'throttle:60,1'
	]
], function() {
	Route::get('/{id?}', 'LotsController@index');
	Route::post('/', 'LotsController@post');
});

//accounts
Route::group([
	'prefix' => 'v1/accounts',
	'middleware' => [
		'auth:api',
		// 'throttle:60,1'
	]
], function() {
	Route::get('/{id?}', 'AccountsController@index');
	Route::post('/', 'AccountsController@postOverride');
});