<?php
namespace App\Validators;

class LotValidator extends BaseValidator
{
	protected $rules = [
		'block_id' => 'required|integer',
		'name' => 'required|string'
	];
	
	protected $messages = [
		'block_id.required' => 'block_id is required',
		'name.required' => 'lot name is required'
	];
}