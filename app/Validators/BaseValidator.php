<?php
namespace App\Validators;

use Validator;
use App\Exceptions\ValidationException;

abstract class BaseValidator
{
	protected $validator;
	
	protected $constraints = [];
	
	public function validate(array $data=[], array $rules=[], array $messages=[])
	{
		if($this->constraints) {
			$this->overrideRules();
		}
		
		// vd($this->constraints);
		
		$rules = $rules ?: $this->rules;
		$messages = $messages ?: $this->messages;
		
		// vd($rules);
		
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
}