<?php
namespace App\Validators;

use App\Models\WaterRate;

class WaterRateValidator extends BaseValidator
{
	private $rules = [
		'min_fee' => 'required_if:min_m3,0|numeric|min:0',
		'per_m3' => 'required|numeric|min:0'
	];
	
	protected $messages = [
		'min_m3.required' => 'minimum m3 value is required',
		'max_m3.required' => 'maximum m3 value is required',
		'per_m3.required' => 'per m3 value is required',
		'max_m3.unique' => 'm3 range has already been added'
	];
	
	public function getRules()
	{
		$maxM3Rules = [
			'required',
			'numeric',
			'gte:min_m3'
		];
		
		$minM3Rules = [
			'required',
			'numeric',
			'min:0',
			'lte:max_m3'
		];
		
		if (!isset($this->data['update_id'])) {
			array_push($maxM3Rules, "unique:water_rates,max_m3,NULL,id,min_m3,{$this->data['min_m3']},deleted_at,NULL");
			
			array_push($minM3Rules, function ($attribute, $value, $fail) {
				$waterRate = WaterRate::whereRaw('? BETWEEN min_m3 AND max_m3', [$this->data['min_m3']])->first();
				if ($waterRate) {
					$fail('water rates conflict error.');
				}
			});
			
		} else {
			array_push($maxM3Rules, "unique:water_rates,max_m3,{$this->data['update_id']},id,min_m3,{$this->data['min_m3']},deleted_at,NULL");
			
			array_push($minM3Rules, function ($attribute, $value, $fail) {
				$waterRate = WaterRate::where('id', '<>', $this->data['update_id'])
					->whereRaw('? BETWEEN min_m3 AND max_m3', [$this->data['min_m3']])
					->first();
					
				if ($waterRate) {
					$fail('water rates conflict error.');
				}
			});
		}
		
		return array_merge($this->rules, [
			'max_m3' => $maxM3Rules,
			'min_m3' => $minM3Rules
		]);
	}
}