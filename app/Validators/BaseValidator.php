<?php
namespace App\Validators;

use Validator;
use App\Exceptions\ValidationException;

abstract class BaseValidator
{
	protected $validator;
	
	private $data;
	
	public function validate($data)
	{
		$this->validator = Validator::make($data, $this->rules, $this->messages);
		
		if($this->validator->fails()) {
			throw new ValidationException($this->validator->errors());
		}
		
		return true;
	}
	
	public function getErrors()
	{
		
	}
}