<?php
namespace App\Validators;

class AccountValidator extends BaseValidator
{
	protected $rules = [
		'parent_id' => 'sometimes|integer',
		'lastname' => 'required|string',
		'firstname' => 'required|string',
		'middlename' => 'sometimes|string',
		'suffix' => 'sometimes|string',
		'email' => 'required|string|email',
		'username' => 'required|string',
		'password' => 'required|string'
	];
	
	protected $messages = [
		'parent_id.integer' => 'parent_id must be integer',
		'lastname.required' => 'lastname is required',
		'firstname.required' => 'firstname is required',
		'middlename.required' => 'middlename is required',
		'email.required' => 'email is required',
		'email.email' => 'invalid email address',
		'username.required' => 'username is required',
		'password.required' => 'password is required'
	];
	
	public static $custom = [
		'rules' => [],
		'messages' => []
	];
}