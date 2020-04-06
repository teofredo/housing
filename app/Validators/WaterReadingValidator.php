<?php
namespace App\Validators;

class WaterReadingValidator extends BaseValidator
{
	protected $rules = [
		'account_id' => 'required|integer',
		'meter_no' => 'required|string',
		'curr_read' => 'required|numeric',
		'reader_id' => 'required|integer'
	];
	
	protected $messages = [
		//
	];
}