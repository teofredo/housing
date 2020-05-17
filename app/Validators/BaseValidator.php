<?php
namespace App\Validators;

use Validator;
use App\Exceptions\ValidationException;
use Illuminate\Support\Str;

abstract class BaseValidator
{
	protected $validator;
	
	protected $constraints = [];
	
	public function validate(array $data=[], array $rules=[], array $messages=[])
	{
		if($this->constraints) {
			$this->overrideRules();
		}
		
		$rules = $rules ?: $this->rules;
		$messages = $messages ?: $this->messages;
		
		$this->validator = Validator::make($data, $rules, $messages);
		
		if($this->validator->fails()) {
			throw new ValidationException($this->validator->errors());
		}
		
		return true;
	}
	
	public function setConstraints(array $constraints=[])
	{
		$this->constraints = $constraints;
		return $this;
	}

	public function __get($fn)
	{
		$fn = 'get' . Str::studly($fn);
		if (method_exists($this, $fn) && is_callable([$this, $fn])) {
			return $this->$fn();
		}
	}
}