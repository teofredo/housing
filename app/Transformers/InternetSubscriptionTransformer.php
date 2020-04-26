<?php
namespace App\Transformers;

use App\Models\InternetSubscription;

class InternetSubscriptionTransformer extends AbstractTransformer
{
	protected $model = InternetSubscription::class;
	
	protected $availableIncludes = ['account', 'plan'];
	
	public function includeAccount(InternetSubscription $model)
	{
		$account = $model->account;
		return $this->item($account, new AccountTransformer);
	}
	
	public function includePlan(InternetSubscription $model)
	{
		$plan = $model->plan;
		return $this->item($plan, new InternetPlanTransformer);
	}
}