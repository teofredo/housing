<?php
namespace App\Validators;

class PaymentValidator extends BaseValidator
{
	private $rules = [
		'account_id' => 'required|integer',
		'or_no' => 'required|string',
		'amount_due' => 'required|numeric|min:0',
		'amount_paid' => 'required|numeric|min:0',
		'current_balance' => 'sometimes|required|numeric|min:0',
		'paid_at' => 'sometimes|required|date_format:Y-m-d',
		'other_payment' => 'sometimes|required|boolean'
	];
	
	protected $messages = [
		// 
	];

	public function getRules()
	{
		return array_merge($this->rules, [
			'due_date' => 'sometimes|required|date_format:' . config('fairchild.formats.due_date')
		]);
	}
}