<?php
namespace App\Validators;

class MonthlyDueValidator extends BaseValidator
{
	protected $rules = [
		// 
	];
	
	protected $messages = [
		//
	];

	protected function getRules()
	{
		return array_merge($this->rules, [
			'due_date' => 'sometimes|date_format:' . config('fairchild.formats.due_date')
		]);
	}
}