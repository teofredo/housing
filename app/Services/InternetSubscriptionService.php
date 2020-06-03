<?php
namespace App\Services;

use App\Models\InternetSubscription;
use Carbon\Carbon;

class InternetSubscriptionService extends AbstractService
{
	protected static $class = __CLASS__;
	
	public function model()
	{
		return InternetSubscription::class;
	}
	
	public function cancelPlan($subscriptionId, $data)
	{
		$subscription = InternetSubscription::findOrFail($subscriptionId);
		$installedAt = $subscription->installed_at;
		
		if (!$installed_at) {
			$subscription->cancelled_at = Carbon::now();
			$subscription->cancel_reason = $data['cancel_reason'];
			$subscription->active = 0;
			$subscription->save();
			return;
		}
		
		$startDate = $subscription->start_date;
		$endDate = $subscription->end_date;
		$monthsLeft = Carbon::now()->diffInMonths($endDate);
		
		//get other fee id
		$fee = FeeService::ins()->findFirst('code', 'other');
		
		$soa = SoaService::ins()->latest([
			'account_id' => $subscription->account_id,
			'due_date' => getDueDate()
		]);
		
		if ($soa) {
			OtherChargeService::ins()->add([
				'account_id' => $subscription->account->account_id,
				'fee_id'
			]);
		}
		
		
		
		
	}
}