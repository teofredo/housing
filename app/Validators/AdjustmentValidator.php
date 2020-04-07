<?php
namespace App\Validators;

class AdjustmentValidator extends BaseValidator
{
	protected $rules = [
		'account_id' => 'required|integer',
		'description' => 'required|string',
		'amount' => 'required|numeric|min:0',
		'due_date' => 'required|date_format:Y-m-d'
	];
	
	protected $messages = [
		//
	];
}