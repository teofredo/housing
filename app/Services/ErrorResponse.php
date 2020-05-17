<?php
namespace App\Services;

use Illuminate\Support\Str;
use Exception;
use App\Exceptions\{
	EmptyResultException
};

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
		
		//auth exception
		if($this->exception instanceof \Illuminate\Auth\AuthenticationException) {
			$this->format['code'] = 401;
		}

		if($this->exception instanceof EmptyResultException) {
			$this->format['code'] = 'EMPTY_RESULT';
		}

		if ($this->exception instanceof \Illuminate\Database\QueryException) {
			// $this->format['message'] = 'SQL Error';
		}
	}
	
	/**
	* instance of http response
	*/
	public function toJson()
	{
		return response()->json([
			'error' => $this->format
		]);
	}

	/**
	* raw json
	*/
	public function toRawJson()
	{
		return json_encode([
			'error' => $this->format
		]);
	}
}