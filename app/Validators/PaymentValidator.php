<?php
namespace App\Validators;

class PaymentValidator extends BaseValidator
{
	protected $rules = [
		'account_id' => 'required|integer',
		'reference_no' => 'required|string',
		'amount_due' => 'required|numeric|min:0',
		'prev_balance' => 'required|numeric|min:0',
		'amount_received' => 'required|numeric|min:0',
		'current_balance' => 'sometimes|numeric|min:0',
		'due_date' => 'required|date_format:Y-m-d',
		'paid_at' => 'required|date_format:Y-m-d'
	];
	
	protected $messages = [
		// 
	];
}