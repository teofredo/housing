<?php
namespace App\Validators;

class ConfigValidator extends BaseValidator
{
	protected $rules = [
		'key' => 'required',
		'value' => 'sometimes|nullable',
		'comment' => 'sometimes|string'
	];
	
	protected $messages = [
		// 
	];
}