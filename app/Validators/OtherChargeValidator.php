<?php
namespace App\Validators;

class OtherChargeValidator extends BaseValidator
{
	protected $rules = [
		'account_id' => 'required|integer',
		'fee_id' => 'required|integer',
		'description' => 'required|string',
		'amount' => 'required|numeric'
	];
	
	protected $messages = [
		//
	];

	public function getRules()
	{
		return array_merge($this->rules, [
			'due_date' => 'required|date_format:' . config('fairchild.formats.due_date')
		]);
	}
}