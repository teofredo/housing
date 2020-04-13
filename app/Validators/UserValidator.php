<?php
namespace App\Validators;

class UserValidator extends BaseValidator
{
	protected $rules = [
		'name' => 'required|string',
		'email' => 'required|email|unique:users',
		'password' => 'required|string',
		'user_type' => 'required|string|in:superadmin,admin,report,account,water-reader'
	];
	
	protected $messages = [
		'name.required' => 'name is required',
		'email.required' => 'email is required',
		'password.required' => 'password is required'
	];
}