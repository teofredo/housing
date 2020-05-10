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

function getNextPaymentDueDate()
{
	if ($dueDate = dbConfig('due-date')) {
		$dueDate = Carbon::parse($dueDate);
		if ($dueDate->isValid()) {
			return $dueDate;
		}
	}

	$dueDate = dbConfig('payment-due');
	if (!$dueDate) {
		throw new \Exception('payment-due must be defined in config');
	}

	switch($dueDate) {
		case 'START_OF_MONTH':
			$dueDate = Carbon::now()->startOfMonth();
			break;
			
		case 'END_OF_MONTH':
			$dueDate = Carbon::now()->endOfMonth();
			break;

		case 'HALF_OF_MONTH':
			$dueDate = Carbon::now()->day(15);
			break;

		default:
			$dueDate = Carbon::now()->day($dueDate);
			break;
	}

	if(!$dueDate->isValid()) {
		throw new \Exception('due date is not valid');
	}

	ConfigService::ins()->add([
		'key' => 'due-date',
		'value' => $dueDate->copy()->format('Y-m-d'),
		'comment' => 'override payment due date'
	]);

	return $dueDate;
}

function dbConfig($key=null)
{
	$config = ConfigService::ins()->findFirst('key', $key);
	return $config->value ?? null;
}

function getDueDate()
{
	return getNextPaymentDueDate();
}

//internet cutoff
function getCutoff()
{
	$cutoff = dbConfig('cut-off');
	$dueDate = getDueDate();
	return $dueDate->day($cutoff);
}