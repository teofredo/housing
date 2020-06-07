<?php
namespace App\Services;

use App\Models\{
	InternetSubscription,
	InternetPlan
};
use Carbon\Carbon;
use App\Models\Fee;

class InternetSubscriptionService extends AbstractService
{
	protected static $class = __CLASS__;
	
	public function model()
	{
		return InternetSubscription::class;
	}
	
	public function getTerminationSummary($subscriptionId)
	{
		$summary = [];
		$subscription = InternetSubscription::findOrFail($subscriptionId);
		
		if ($subscription->installed_at) {
			$monthsLeft = Carbon::now()->diffInMonths($subscription->end_date);
			$dueDate = getDueDate();
			
			$soa = SoaService::ins()->latest([
				'account_id' => $subscription->account_id,
				'due_date' => $dueDate
			]);
			
			if (!$soa) {
				$monthsLeft++;
			} else {
				$dueDate = nextDueDate(getDueDate(true));
			}
			
			// pre-termination fee
			$preTerminationFee = 0;
			$summary['with_pre_termination_fee'] = false;
			if (Carbon::now()->lte($subscription->end_date)) {
				$preTerminationFee = $subscription->plan->monthly * $monthsLeft;
				$summary['with_pre_termination_fee'] = true;
				$summary['pre_termination_fee'] = $preTerminationFee;
			}
			
			$summary['due_date'] = $dueDate;
			$summary['months_left'] = $monthsLeft;
		}
		
		return (object) [
			'summary' => (object) $summary,
			'model' => $subscription
		];
	}
	
	public function cancelPlan($subscriptionId, array $data)
	{
		$result = $this->getTerminationSummary($subscriptionId);
		$summary = $result->summary;
		$subscription = $result->model;
		
		if ($subscription->installed_at) {
			
			// pre-termination fee
			if ($summary->with_pre_termination_fee) {
				
				$fee = FeeService::ins()->findFirst('code', 'pre_termination_fee');
				if (!$fee) {
					throw new \Exception('undefined pre_termination_fee code.');
				}
				
				// add other info to summary
				$summary->plan = $subscription->plan->name;
				$summary->monthly = $subscription->plan->monthly;
				$summary->start_date = $subscription->start_date;
				$summary->end_date = $subscription->end_date;
				$summary->current_date = Carbon::now();
				
				OtherChargeService::ins()->add([
					'account_id' => $subscription->account->account_id,
					'fee_id' => $fee->fee_id,
					'description' => 'pre-termination fee',
					'due_date' => $summary->due_date,
					'amount' => $summary->pre_termination_fee,
					'data' => json_encode($summary)
				]);
			}
		}
		
		$subscription->cancelled_at = Carbon::now();
		$subscription->cancel_reason = $data['cancel_reason'];
		$subscription->active = null;
		$subscription->save();
		
		return $subscription;
	}
	
	public function changePlan($subscriptionId, array $data)
	{
		// current plan
		$subscription = InternetSubscription::findOrFail($subscriptionId);
		
		// new plan
		$plan = InternetPlan::findOrFail($data['plan_id']);
		$action = $plan->monthly > $subscription->plan->monthly ? 'upgrade' : 'downgrade';
		
		$dueDate = getDueDate();
		$soa = SoaService::ins()->latest([
			'account_id' => $subscription->account_id,
			'due_date' => $dueDate
		]);
		
		if ($soa) {
			$dueDate = nextDueDate(getDueDate(true));
		} else {
			// compute pro-rated of current plan on before changed
			
			$cutoff = getCutoff();
			$prevCutoff = $cutoff->copy()->subMonthNoOverflow();

			//no of days from prev to current cutoff
			$ndays = $cutoff->diffInDays($prevCutoff);

			//no of days from plan start_date to cut off
			$n = $subscription->start_date->diffInDays($cutoff);

			//get per day and amount due
			$perDay = $subscription->plan->monthly / $ndays;

			//pro rated
			$proRatedAmount = $perDay * $n;
			
			if ($n >= $ndays) {
				$proRatedAmount = $subscription->plan->monthly;
			}

			//get pro-rated fee id
			$fee = FeeService::ins()->findFirst('code', 'pro_rated');
			
			// charge pro-rated
			OtherChargeService::ins()->add([
				'account_id' => $subscription->account->account_id,
				'fee_id' => $fee->fee_id,
				'description' => 'cancelled internet plan (pro-rated)',
				'due_date' => $dueDate,
				'amount' => $proRatedAmount,
				'data' => json_encode([
					'plan' => $subscription->plan->name,
					'monthly' => $subscription->plan->monthly,
					'cut_off' => $cutoff,
					'prev_cutoff' => $prevCutoff,
					'ndays' => $ndays,
					'start_date' => $subscription->start_date,
					'pro_rated_days' => $n,
					'per_day' => $perDay,
					'amount_due' => $proRatedAmount
				])
			]);
		}
		
		if ($action == 'downgrade') {
			$fee = FeeService::ins()->findFirst('code', 'downgrade_fee');
			if (!$fee) {
				throw new \Exception('undefined downgrade_fee code');
			}
			
			// charge downgrade fee
			OtherChargeService::ins()->add([
				'account_id' => $subscription->account->account_id,
				'fee_id' => $fee->fee_id,
				'description' => 'internet change plan - downgrade fee',
				'due_date' => $dueDate,
				'amount' => $fee->fee,
				'data' => json_encode([
					'from' => $subscription->plan->name,
					'to' => $plan->name
				])
			]);
		}
		
		$fee = FeeService::ins()->findFirst('code', 'installation_fee');
		if (!$fee) {
			throw new \Exception('undefined installation_fee code');
		}
		
		// charge installation fee
		OtherChargeService::ins()->add([
			'account_id' => $subscription->account_id,
			'fee_id' => $fee->fee_id,
			'description' => "new {$plan->name} - installation fee",
			'due_date' => $dueDate,
			'amount' => $fee->fee,
			'data' => json_encode([
				'from' => $subscription->plan->name,
				'to' => $plan->name,
				'action' => $action
			])
		]);
		
		// cancel current plan
		$subscription->active = null;
		$subscription->cancelled_at = Carbon::now();
		$subscription->cancel_reason = $action;
		$subscription->save();
		
		// add new plan
		$newSubscription = InternetSubscription::create([
			'account_id' => $subscription->account_id,
			'plan_id' => $plan->plan_id,
			'start_date' => Carbon::now(),
			'end_date' => Carbon::now()->addYearNoOverflow(),
			'active' => 1,
			'last_subscription_id' => $subscription->subscription_id,
			'installed_at' => $subscription->installed_at
		]);
		
		return [
			'last_subscription_id' => $subscription->subscription_id,
			'subscription_id' => $newSubscription->subscription_id
		];
	}
}