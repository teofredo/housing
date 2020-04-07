<?php

use Illuminate\Support\Str;
use Carbon\Carbon;

function pr(array $data)
{
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	die;
}

function vd($data)
{
	var_dump($data);
	die;
}

function getNextPaymentDueDate()
{
	$dueDate = dbConfig('payment-due');
	if(!$dueDate) {
		throw new \Exception('payment-due must be defined in config');
	}

	if(Str::containsAll($dueDate->value, ['[', ']', '30'])) {
		return Carbon::now()->endOfMonth();
	} 

	return Carbon::now()->day($dueDate->value);
}

function dbConfig($key=null)
{
	return \App\Models\Config::where('key', $key)->first();
}