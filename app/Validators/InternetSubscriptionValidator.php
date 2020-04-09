<?php
namespace App\Validators;

class InternetSubscriptionValidator extends BaseValidator
{
	protected $rules = [
		'account_id' => 'required|integer',
		'plan_id' => 'required|integer',
		'installed' => 'sometimes|integer',
		'start_date' => 'required|date_format:Y-m-d',
		'end_date' => 'required|date_format:Y-m-d',
		'cancel_reason' => 'required_with:cancelled_at|string',
		'active' => 'sometimes|integer',
		'installed_at' => 'sometimes|date'
	];
	
	protected $messages = [
		//use laravel's default error messages
	];
}