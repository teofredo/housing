<?php
namespace App\Validators;

class InternetSubscriptionValidator extends BaseValidator
{
	private $rules = [
		'account_id' => 'required|integer|unique:internet_subscriptions,account_id,NULL,subscription_id,active,1,deleted_at,NULL',
		'plan_id' => 'required|integer',
		'installed' => 'sometimes|integer',
		'start_date' => 'required|date_format:Y-m-d',
		'end_date' => 'required|date_format:Y-m-d',
		'cancel_reason' => 'sometimes|required|string',
		'active' => 'sometimes|integer',
		'installed_at' => 'sometimes|required|date'
	];
	
	protected $messages = [
		'account_id.unique' => 'The account has already subscribed to a plan.'
	];
	
	public function getRules()
	{
		if (isset($this->data['update_id'])) {
			$this->rules['account_id'] = [
				'required',
				'integer',
				"unique:internet_subscriptions,account_id,{$this->data['update_id']},subscription_id,active,1,deleted_at,NULL"
			];
		}
		
		return $this->rules;
	}
}