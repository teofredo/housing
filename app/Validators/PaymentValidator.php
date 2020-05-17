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
		'paid_at' => 'required|date_format:Y-m-d'
	];
	
	protected $messages = [
		// 
	];

	protected function getRules()
	{
		return array_merge($this->rules, [
			'due_date' => 'required|date_format:' . config('fairchild.formats.due_date')
		]);
	}
}