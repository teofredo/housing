<?php
namespace App\Validators;

class WaterReadingValidator extends BaseValidator
{
	protected $rules = [
		'account_id' => 'required|integer',
		'meter_no' => 'required|string',
		'curr_read' => 'required|numeric'
	];
	
	protected $messages = [
		'due_date.unique' => 'water reading for this due date already done.'
	];

	protected function overrideRules()
	{
		$this->rules['due_date'] = [
			'required',
			'date_format:' . config('fairchild.formats.due_date'),
			"unique:water_readings,due_date,NULL,id,account_id,{$this->constraints['account_id']}"
		];
	}
}