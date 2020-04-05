<?php
namespace App\Validators;

class AccountValidator extends BaseValidator
{
	protected $rules = [
		'email' => 'required|string|unique:accounts,email',
		'password' => 'required|string',
		
		//householder rules
		'householder_type' => 'required|string|in:owner,tenant',
		'lastname' => 'required|string',
		'firstname' => 'required|string',
		'middlename' => 'sometimes|string',
		'suffix' => 'sometimes|string',
		'contact_no' => 'required|string',
		'block_id' => 'required|integer',
		'lot_id' => 'required|integer',
		'house_no' => 'sometimes|string',
		'moved_in' => 'sometimes|string'
	];
	
	protected $messages = [
		'email.required' => 'email is required',
		'email.email' => 'invalid email address',
		'email.unique' => 'email address is already taken',
		'password.required' => 'password is required',
		
		//householder feedbacks
		'householder_type.required' => 'householder_type is required',
		'householder_type.in' => 'invalid householder_type',
		'lastname.required' => 'lastname is required',
		'firstname.required' => 'firstname is required',
		'contact_no.required' => 'contact_no is required',
		'block_id.required' => 'block_id is required',
		'block_id.integer' => 'block_id must be integer',
		'lot_id.required' => 'lot_id is required',
		'lot_id.integer' => 'lot_id must be integer'
	];
	
	protected function overrideRules()
	{
		$this->rules['lot_id'] = "required|integer|unique:App\Models\Householder,lot_id,NULL,id,block_id,{$this->constraints['block_id']},deleted_at,NULL";
		$this->messages['lot_id.unique'] = 'the unit has already been taken';
	}
}