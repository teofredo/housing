<?php
namespace App\Validators;

class WaterReadingValidator extends BaseValidator
{
	protected $rules = [
		'account_id' => 'required|integer',
		'meter_no' => 'required|string',
		'curr_read' => 'required|numeric',
		'due_date' => 'required|date_format:Y-m-d'
	];
	
	protected $messages = [
		'due_date.unique' => 'water reading for this due date already done.'
	];

	protected function overrideRules()
	{
		$this->rules['due_date'] = "required|string|unique:water_readings,due_date,NULL,id,account_id,{$this->constraints['account_id']}";
	}
}