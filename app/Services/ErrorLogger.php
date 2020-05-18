<?php
namespace App\Services;

use App\Interfaces\LoggerInterface;
use App\Models\ErrorLog;

class ErrorLogger implements LoggerInterface
{
	private static $class = __CLASS__;

	private static $instance = null;

	public static function ins()
	{
		if (!self::$instance instanceof self::$class) {
			self::$instance = new self::$class;
		}

		return self::$instance;
	}

	public function log($error, $trace=null, $request=null)
	{
		ErrorLog::create([
			'error' => $error,
			'trace' => $trace,
			'request' => $request
		]);
	}
}