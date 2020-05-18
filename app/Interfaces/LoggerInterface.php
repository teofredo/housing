<?php
namespace App\Interfaces;

interface LoggerInterface
{
	public function log($error, $trace=null, $request=null);
}