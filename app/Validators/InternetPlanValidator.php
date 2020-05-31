<?php
namespace App\Validators;

class InternetPlanValidator extends BaseValidator
{
	private $rules = [
		'name' => 'required|string|unique:internet_plans,name,NULL,plan_id,deleted_at,NULL',
		'monthly' => 'required|numeric|min:0',
		'mbps' => 'required|numeric|max:100|min:0'
	];
	
	protected $messages = [
		'name.required' => 'plan name is required',
		'name.unique' => 'plan already exists',
		'monthly.required' => 'monthly price is required',
		'monthly.numeric' => 'invalid monthly price',
		'mbps.required' => 'mbps is required',
		'mbps.numeric' => 'invalid mbps value',
		'mbps.max' => 'mbps exceeds maximum limit '
	];
	
	public function getRules()
	{
		if (isset($this->data['update_id'])) {
			$this->rules['name'] = [
				'required',
				'string',
				"unique:internet_plans,name,{$this->data['update_id']},plan_id,deleted_at,NULL"
			];
		}
		
		return $this->rules;
	}
}