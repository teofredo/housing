<?php
namespace App\Validators;

class InternetPlanValidator extends BaseValidator
{
	protected $rules = [
		'name' => 'required|string|unique:internet_plans,name',
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
}