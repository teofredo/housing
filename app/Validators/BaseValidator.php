<?php
namespace App\Validators;

use Validator;
use App\Exceptions\ValidationException;

abstract class BaseValidator
{
	protected $validator;
	
	private $data;
	
	public function validate(array $data=[], array $rules=[], array $messages=[])
	{
		$rules = $rules ?: $this->rules;
		$messages = $messages ?: $this->messages;
		
		$this->validator = Validator::make($data, $rules, $messages);
		
		if($this->validator->fails()) {
			throw new ValidationException($this->validator->errors());
		}
		
		return true;
	}
}