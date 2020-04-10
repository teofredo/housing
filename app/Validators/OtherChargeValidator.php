<?php
namespace App\Validators;

class OtherChargeValidator extends BaseValidator
{
	protected $rules = [
		'account_id' => 'required|integer',
		'fee_id' => 'required|integer',
		'description' => 'required|string',
		'amount' => 'required|numeric',
		'due_date' => 'required|date_format:Y-m-d'
	];
	
	protected $messages = [
		//
	];
}