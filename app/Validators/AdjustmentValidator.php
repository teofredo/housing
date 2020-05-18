<?php
namespace App\Validators;

class AdjustmentValidator extends BaseValidator
{
	protected $rules = [
		'account_id' => 'required|integer',
		'description' => 'required|string',
		'amount' => 'required|numeric|min:0'
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