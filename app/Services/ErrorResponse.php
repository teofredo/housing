<?php
namespace App\Services;

use Illuminate\Support\Str;
use Exception;

class ErrorResponse
{
	private $exception;
	
	private $format = [];
	
	public function __construct(Exception $exception)
	{
		$this->exception = $exception ?? null;
		
		$this->format();
	}
	
	private function format()
	{
		if(!$this->exception) {
			return;
		}
		
		$this->format = [
			'type' 	=> get_class($this->exception),
			'code' => $this->exception->getCode(),
			'message' => $this->exception->getMessage(),
			'file' => $this->exception->getFile(),
			'line' => $this->exception->getLine()
		];
		
		//duplicate entry exception
		if($this->exception instanceof \Illuminate\Database\QueryException
			&& $this->format['code'] == 23000
			&& Str::startsWith($this->format['message'], 'SQLSTATE[23000]')) {
			$this->format['message'] = 'Duplicate entry';
		}
	}
	
	public function toJson()
	{
		return response()->json([
			'error' => $this->format
		]);
	}
}