<?php
namespace App\Validators;

class AccountValidator extends BaseValidator
{
	protected $rules = [
		'email' => 'required|string|unique:accounts,email',
		'password' => 'sometimes|string',
		
		//householder rules
		'householder_type' => 'required|string|in:owner,tenant',
		'lastname' => 'required|string',
		'firstname' => 'required|string',
		'middlename' => 'sometimes|string|nullable',
		'suffix' => 'sometimes|string|nullable',
		'contact_no' => 'required|string',
		'block_id' => 'required|integer',
		'lot_id' => 'required|integer',
		'moved_in' => 'required|date_format:Y-m-d',
		'house_no' => 'sometimes|string|max:20',
		'water_meter_no' => 'sometimes|string|max:20'
	];
	
	protected $messages = [
		'email.required' => 'email is required',
		'email.email' => 'invalid email address',
		'email.unique' => 'email address is already taken',
		// 'password.required' => 'password is required',
		
		//householder feedbacks
		'householder_type.required' => 'householder_type is required',
		'householder_type.in' => 'invalid householder_type',
		'lastname.required' => 'lastname is required',
		'firstname.required' => 'firstname is required',
		'contact_no.required' => 'contact_no is required',
		'block_id.required' => 'block_id is required',
		'block_id.integer' => 'block_id must be integer',
		'lot_id.required' => 'lot_id is required',
		'lot_id.integer' => 'lot_id must be integer',
		'lot_id.unique' => 'the house has already been taken'
	];
	
	protected function overrideRules()
	{
		$this->rules['lot_id'] = "required|integer|unique:App\Models\Householder,lot_id,NULL,id,block_id,{$this->constraints['block_id']},deleted_at,NULL";
	}
}