<?php
namespace App\Validators;

class MonthlyDueValidator extends BaseValidator
{
	protected $rules = [
		'due_date' => 'sometimes|date_format:Y-m-d'
	];
	
	protected $messages = [
		//
	];
}