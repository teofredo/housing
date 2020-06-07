<?php
namespace App\Services;

use Illuminate\Support\Str;
use Exception;
use App\Exceptions\{
	EmptyResultException
};
use Illuminate\Http\Request;

class ErrorResponse
{
	private $exception;
	
	private $format = [];

	private $trace = null;

	private $request;
	
	public function __construct(Exception $exception, $request=null)
	{
		$this->exception = $exception ?? null;
		
		$this->request = $request instanceof Request ? $request->all() : $request;
		
		$this->format()
			->log()
			->intercept();
	}
	
	private function format()
	{
		if(!$this->exception) {
			return $this;
		}
		
		$this->format = [
			'type' 	=> get_class($this->exception),
			'code' => $this->exception->getCode(),
			'message' => $this->exception->getMessage(),
			'file' => $this->exception->getFile(),
			'line' => $this->exception->getLine()
		];

		// used for logging only
		$this->trace = $this->exception->getTrace();

		return $this;
	}

	/**
	* log error to db
	* use errorLogger service
	* params > error, trace, request
	*/
	private function log()
	{
		ErrorLogger::ins()->log(
			json_encode($this->format),
			json_encode($this->trace),
			json_encode($this->request)	
		);

		return $this;
	}

	private function intercept()
	{
		//auth exception
		if($this->exception instanceof \Illuminate\Auth\AuthenticationException) {
			$this->format['code'] = 401;
		}

		if ($this->exception instanceof \Illuminate\Database\QueryException) {
			$this->format['message'] = 'SQL Error';
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