<?php

Route::group([
	'prefix' => 'v1',
	'middleware' => [
		'throttle:60,1',
		// 'auth.provider',
		// 'cors'
	]
], function($r) {
	$r->post('/login', 'UsersController@login');
});

Route::group([
	'prefix' => 'v1',
	'middleware' => [
		'auth:api',
		'throttle:60,1',
		// 'cors'
	]
], function($r) {
	$r->get('/auth-user', 'AuthController@user');
	$r->post('/logout', 'AuthController@logout');
	
	$r->prefix('blocks')->group(function($r){
		$r->get('/{id?}', 'BlocksController@index');
		$r->post('/', 'BlocksController@post');
		$r->put('/{id?}', 'BlocksController@put');
		$r->delete('/{id?}', 'BlocksController@delete');
	});
	
	$r->prefix('lots')->group(function($r){
		$r->get('/{id?}', 'LotsController@index');
		$r->post('/', 'LotsController@post');
		$r->put('/{id?}', 'LotsController@put');
		$r->delete('/{id?}', 'LotsController@delete');
	});
	
	$r->prefix('accounts')->group(function($r){
		$r->get('/{id?}', 'AccountsController@index');
		$r->post('/', 'AccountsController@postOverride');
	});
	
	$r->prefix('householders')->group(function($r){
		$r->get('/{id?}', 'HouseholdersController@index');
	});
	
	$r->prefix('internet-plans')->group(function($r){
		$r->get('/{id?}', 'InternetPlansController@index');
		$r->post('/', 'InternetPlansController@post');
		$r->put('/{id?}', 'InternetPlansController@put');
		$r->delete('/{id?}', 'InternetPlansController@delete');
	});
	
	$r->prefix('water-rates')->group(function($r){
		$r->get('/{id?}', 'WaterRatesController@index');
		$r->post('/', 'WaterRatesController@post');
		$r->put('/{id?}', 'WaterRatesController@putOverride');
		$r->delete('/{id?}', 'WaterRatesController@delete');
	});
	
	$r->prefix('fees')->group(function($r){
		$r->get('/{id?}', 'FeesController@index');
		$r->post('/', 'FeesController@post');
		$r->put('/{id?}', 'FeesController@put');
		$r->delete('/{id?}', 'FeesController@delete');
	});
	
	$r->prefix('internet-subscriptions')->group(function($r){
		$r->get('/{id?}', 'InternetSubscriptionsController@index');
		$r->post('/', 'InternetSubscriptionsController@post');
		$r->put('/{id?}', 'InternetSubscriptionsController@put');
		$r->delete('/{id?}', 'InternetSubscriptionsController@delete');
	});
	
	$r->prefix('water-readings')->group(function($r){
		$r->get('/{id?}', 'WaterReadingsController@index');
		$r->post('/', 'WaterReadingsController@postOverride');
		$r->put('/{id?}', 'WaterReadingsController@putOverride');
		$r->delete('/{id?}', 'WaterReadingsController@delete');
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
		$r->put('/{id?}', 'PaymentsController@putOverride');
	});

	$r->prefix('process')->group(function($r){
		$r->get('/{id?}', 'ProcessController@index');
	});
	
	$r->prefix('users')->group(function($r){
		$r->get('/{id?}', 'UsersController@index');
		$r->post('/', 'UsersController@postOverride');
	});

	$r->prefix('config')->group(function($r){
		$r->get('/{id?}', 'ConfigController@index');
		$r->post('/', 'ConfigController@postOverride');
	});

	$r->prefix('soa')->group(function($r){
		$r->get('/{id?}', 'SoaController@index');
	});
});