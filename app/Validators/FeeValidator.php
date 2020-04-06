<?php
namespace App\Validators;

class FeeValidator extends BaseValidator
{
	protected $rules = [
		'name' => 'required|string|unique:fees,name',
		'fee' => 'required|numeric|min:0',
		'other_fee' => 'sometimes|integer|in:0,1'
	];
	
	protected $messages = [
		'name.required' => 'fee name is required',
		'name.unique' => 'fee name already added',
		'fee.required' => 'fee amount is required'
	];
}