<?php

use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\ConfigService;

define('DUE_DATE_FORMAT', 'm/Y');

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

function nextDueDate(Carbon $date=null, $carbonize=false)
{
	if ($date && $date instanceof Carbon) {
		$nextDueDate = $date->copy()->addMonthNoOverflow();
	} else {
		//get carbonized due date and add month
		$nextDueDate = getDueDate(true)->addMonthNoOverflow();
	}

	return $carbonize ? $nextDueDate : $nextDueDate->format(DUE_DATE_FORMAT);
}

function dbConfig($key=null)
{
	$config = ConfigService::ins()->findFirst('key', $key);
	return $config->value ?? null;
}

function getDueDate($carbonize=false)
{
	$dueDate = dbConfig('due-date');
	if (!$dueDate) {
		throw new \Exception('current due date not set in config');	
	}

	return $carbonize ? myCarbonize($dueDate) : $dueDate;
}

/**
* myCarbonize > month-year-carbonize
*/
function myCarbonize($myformatted, $delimiter='/')
{
	list($month, $year) = explode($delimiter, $myformatted);
	return Carbon::createFromDate($year, $month, 1);
}

//internet cutoff
function getCutoff($date=null)
{
	$cutoff = dbConfig('cut-off');

	if ($date && $date instanceof \Carbon\Carbon) {
		return $date->day($cutoff);		
	}

	$dueDate = getDueDate(true);
	return $dueDate->day($cutoff);
}

function getPaymentDue()
{
	$dueDate = getDueDate(true);
	$paymentDue = dbConfig('payment-due');

	if (is_int($paymentDue)) {
		return $dueDate->day($paymentDue);
	}

	switch($paymentDue) {
		case 'START_OF_MONTH':
			return $dueDate;

		case 'HALF_OF_MONTH':
			return $dueDate->day(15);

		case 'END_OF_MONTH':
		default:
			// return $dueDate->endOfMonth();
	}

	return $dueDate->endOfMonth();
}