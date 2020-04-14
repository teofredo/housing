<?php

Route::group([
	'prefix' => 'v1',
	'middleware' => [
		'throttle:60,1',
		// 'auth.provider'
	]
], function($r) {
	$r->post('/login', 'UsersController@login');
	
	$r->group(['prefix' => 'auth'], function($r){
		$r->get('/user', 'AuthController@user')->middleware('auth:api');
		$r->get('/account', 'AuthController@user')->middleware('auth:accounts');
	});
});

Route::group([
	'prefix' => 'v1',
	'middleware' => [
		'auth:api',
		'throttle:60,1'
	]
], function($r) {
	
	$r->post('/logout', 'AuthController@logout');
	
	$r->prefix('blocks')->group(function($r){
		$r->get('/{id?}', 'BlocksController@index');
		$r->post('/', 'BlocksController@post');
	});
	
	$r->prefix('lots')->group(function($r){
		$r->get('/{id?}', 'LotsController@index');
		$r->post('/', 'LotsController@postOverride');
	});
	
	$r->prefix('accounts')->group(function($r){
		$r->get('/{id?}', 'AccountsController@index');
		$r->post('/', 'AccountsController@postOverride');
	});
	
	$r->prefix('internet-plans')->group(function($r){
		$r->get('/{id?}', 'InternetPlansController@index');
		$r->post('/', 'InternetPlansController@post');
	});
	
	$r->prefix('water-rates')->group(function($r){
		$r->get('/{id?}', 'WaterRatesController@index');
		$r->post('/', 'WaterRatesController@postOverride');
	});
	
	$r->prefix('fees')->group(function($r){
		$r->get('/{id?}', 'FeesController@index');
		$r->post('/', 'FeesController@post');
	});
	
	$r->prefix('internet-subscriptions')->group(function($r){
		$r->get('/{id?}', 'InternetSubscriptionsController@index');
		$r->post('/', 'InternetSubscriptionsController@postOverride');
	});
	
	$r->prefix('water-readings')->group(function($r){
		$r->get('/{id?}', 'WaterReadingsController@index');
		$r->post('/', 'WaterReadingsController@postOverride');
	});

	$r->prefix('other-charges')->group(function($r){
		$r->get('/{id?}', 'OtherChargesController@index');
		$r->post('/', 'OtherChargesController@postOverride');
	});

	$r->prefix('adjustments')->group(function($r){
		$r->get('/{id?}', 'AdjustmentsController@index');
		$r->post('/', 'AdjustmentsController@post');
	});

	$r->prefix('monthly-dues')->group(function($r){
		$r->get('/{id?}', 'MonthlyDuesController@index');

		//using commands
		$r->post('/', 'MonthlyDuesController@postOverride');
	});

	$r->prefix('payments')->group(function($r){
		$r->get('/{id?}', 'PaymentsController@index');
		$r->post('/', 'PaymentsController@postOverride');
	});

	$r->prefix('process')->group(function($r){
		$r->get('/{id?}', 'ProcessController@index');
	});
	
	$r->prefix('users')->group(function($r){
		$r->get('/{id?}', 'UsersController@index');
		$r->post('/', 'UsersController@postOverride');
	});
});