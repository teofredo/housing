<?php

use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\ConfigService;

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

function getDueByDate(Carbon $date)
{
	if (!$date->isValid()) {
		throw new \Exception('the date is invalid');
	}

	$dueDate = dbConfig('payment-due');
	if (!$dueDate) {
		throw new \Exception('payment-due must be defined in config');
	}

	switch($dueDate) {
		case 'START_OF_MONTH':
			$dueDate = $date->startOfMonth();
			break;
			
		case 'END_OF_MONTH':
			$dueDate = $date->endOfMonth();
			break;

		case 'HALF_OF_MONTH':
			$dueDate = $date->day(15);
			break;

		default:
			$dueDate = $date->day($dueDate);
			break;
	}

	if(!$dueDate->isValid()) {
		throw new \Exception('due date is not valid');
	}

	return $dueDate;
}

function dbConfig($key=null)
{
	$config = ConfigService::ins()->findFirst('key', $key);
	return $config->value ?? null;
}

function getDueDate()
{
	$dueDate = dbConfig('due-date');
	if (!$dueDate) {
		throw new \Exception('Due date is not set in config');
	}

	$dueDate = Carbon::parse($dueDate);
	if (!$dueDate->isValid()) {
		throw new \Exception('Invalid due date in config');
	}

	return $dueDate;

	/*$dueDate = getDueByDate(Carbon::now());

	ConfigService::ins()->add([
		'key' => 'due-date',
		'value' => $dueDate->copy()->format('Y-m-d'),
		'comment' => 'override payment due date'
	]);

	return $dueDate;*/
}

//internet cutoff
function getCutoff($date=null)
{
	$cutoff = dbConfig('cut-off');

	if ($date && $date instanceof \Carbon\Carbon) {
		return $date->day($cutoff);		
	}

	$dueDate = getDueDate();
	return $dueDate->day($cutoff);
}