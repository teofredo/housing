<?php
namespace App\Services;

use App\Models\InternetSubscription;

class InternetSubscriptionService extends AbstractService
{
	protected static $class = __CLASS__;
	
	public function model()
	{
		return InternetSubscription::class;
	}
}