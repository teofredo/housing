<?php
namespace App\Validators;

class UserValidator extends BaseValidator
{
	protected $rules = [
		'name' => 'required|string',
		'username' => 'required|string|unique:users,username',
		'email' => 'sometimes|email|unique:users,email',
		'password' => 'required|string',
		'user_type' => 'required|string|in:superadmin,admin,report,water-reader'
	];
	
	protected $messages = [
		//
	];
}