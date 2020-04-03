<?php
namespace App\Validators;

class BlockValidator extends BaseValidator
{
	protected $rules = [
		'name' => 'required|string|unique:blocks'
	];
	
	protected $messages = [
		'name.required' => 'block name is required',
		'name.unique' => 'block name already exists'
	];
}