<?php
namespace App\Validators;

class WaterRateValidator extends BaseValidator
{
	protected $rules = [
		'min_fee' => 'sometimes|numeric|min:0',
		'min_m3' => 'required|numeric|lte:max_m3',
		'max_m3' => 'required|numeric|gte:min_m3',
		'per_m3' => 'required|numeric|min:0'
	];
	
	protected $messages = [
		'min_m3.required' => 'minimum m3 value is required',
		'max_m3.required' => 'maximum m3 value is required',
		'per_m3.required' => 'per m3 value is required',
		'max_m3.unique' => 'm3 range has already been added'
	];
	
	protected function overrideRules()
	{
		$this->rules['max_m3'] = "required|numeric|gte:min_m3|unique:water_rates,max_m3,NULL,id,min_m3,{$this->constraints['min_m3']}";		
	}
}