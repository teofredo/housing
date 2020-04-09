<?php

Route::group([
	'prefix' => 'v1',
	'middleware' => [
		'throttle:60,1'
	]
], function() {
	Route::post('/signup', 'AuthController@signup');
	Route::post('/login', 'AuthController@login');
});

Route::group([
	'prefix' => 'v1',
	'middleware' => [
		'auth:api',
		'throttle:60,1'
	]
], function() {
	Route::get('/user', 'AuthController@user');
	Route::post('/logout', 'AuthController@logout');
	
	Route::prefix('blocks')->group(function(){
		Route::get('/{id?}', 'BlocksController@index');
		Route::post('/', 'BlocksController@post');
	});
	
	Route::prefix('lots')->group(function(){
		Route::get('/{id?}', 'LotsController@index');
		Route::post('/', 'LotsController@postOverride');
	});
	
	Route::prefix('accounts')->group(function(){
		Route::get('/{id?}', 'AccountsController@index');
		Route::post('/', 'AccountsController@postOverride');
	});
	
	Route::prefix('internet-plans')->group(function(){
		Route::get('/{id?}', 'InternetPlansController@index');
		Route::post('/', 'InternetPlansController@post');
	});
	
	Route::prefix('water-rates')->group(function(){
		Route::get('/{id?}', 'WaterRatesController@index');
		Route::post('/', 'WaterRatesController@postOverride');
	});
	
	Route::prefix('fees')->group(function(){
		Route::get('/{id?}', 'FeesController@index');
		Route::post('/', 'FeesController@post');
	});
	
	Route::prefix('internet-subscriptions')->group(function(){
		Route::get('/{id?}', 'InternetSubscriptionsController@index');
		Route::post('/', 'InternetSubscriptionsController@postOverride');
	});
	
	Route::prefix('water-readings')->group(function(){
		Route::get('/{id?}', 'WaterReadingsController@index');
		Route::post('/', 'WaterReadingsController@postOverride');
	});

	Route::prefix('other-charges')->group(function(){
		Route::get('/{id?}', 'OtherChargesController@index');
		Route::post('/', 'OtherChargesController@postOverride');
	});

	Route::prefix('adjustments')->group(function(){
		Route::get('/{id?}', 'AdjustmentsController@index');
		Route::post('/', 'AdjustmentsController@post');
	});

	Route::prefix('monthly-dues')->group(function(){
		Route::get('/{id?}', 'MonthlyDuesController@index');
		Route::post('/', 'MonthlyDuesController@postOverride');
	});
});