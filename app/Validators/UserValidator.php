<?php
namespace App\Validators;

class UserValidator extends BaseValidator
{
	protected $rules = [
		'name' => 'required|string',
		'email' => 'required|string|unique:users',
		'password' => 'required|string'
	];
	
	protected $messages = [
		'name.required' => 'name is required',
		'email.required' => 'email is required',
		'password.required' => 'password is required'
	];
}