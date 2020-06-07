<?php
namespace App\Validators;

class WaterReadingValidator extends BaseValidator
{
	private $rules = [
		'account_id' => 'sometimes|required|integer',
		'meter_no' => 'sometimes|required|string',
		'curr_read' => 'required|numeric'
	];
	
	protected $messages = [
		'due_date.unique' => 'a reading already made for the selected due date'
	];
	
	public function getRules()
	{
		$dueDateRules = [
			'sometimes',
			'required',
			'date_format:' . config('fairchild.formats.due_date')
		];
		
		// post
		if (!isset($this->data['update_id'])) {
			array_push($dueDateRules, "unique:water_readings,due_date,NULL,id,account_id,{$this->data['account_id']}");
		
		// put
		} else {
			array_push($dueDateRules, "unique:water_readings,due_date,{$this->data['update_id']},id,account_id,{$this->data['account_id']}");
		}
		
		return array_merge($this->rules, [
			'due_date' => $dueDateRules
		]);
	}
}