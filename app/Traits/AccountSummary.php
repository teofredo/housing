<?php
namespace App\Traits;

use App\Models\{
	Account,
	OtherCharge
};
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Services\{
	WaterReadingService,
	OtherChargeService,
	InternetSubscriptionService,
	PaymentService,
	AdjustmentService,
	FeeService
};

trait AccountSummary
{
	public function summarize(Account $account, $dueDate=null)
	{
		$this->account = $account;
		$this->dueDate = $dueDate ?? $this->dueDate;

		try {
			DB::beginTransaction();

			$summary = [
				'water' => $this->getWaterBill(),
				'internet' => $this->getInternetBill(),
				'other_charges' => $this->getOtherCharges(),
				'prev_balance' => $this->getPrevBalance(),
				'penalty' => $this->getPenalty(),
				'adjustments' => $this->getAdjustments()
			];

			DB::commit();

			return $summary;

		} catch(\Exception $e) {}

		DB::rollBack();

		throw $e;
	}

	protected function getWaterBill()
	{
		return WaterReadingService::ins()->first([
			'account_id' => $this->account->account_id,
			'due_date' => $this->dueDate
		]);
	}

	protected function getInternetBill()
	{
		$internet = InternetSubscriptionService::ins()
			->getModel()
			->where([
				'account_id' => $this->account->account_id,
				'active' => 1,
			])
			->whereNotNull('installed_at')
			->first();

		if (!$internet) {
			return $internet;
		};
		
		$installedAt = Carbon::parse($internet->installed_at);
		$cutoff = getCutoff();
		$prevCutoff = $cutoff->copy()->subMonthNoOverflow();

		//no of days considered as pro rated
		$proRated = (int) dbConfig('pro-rated');

		//no of days from prev to current cutoff
		$ndays = $cutoff->diffInDays($prevCutoff);

		//no of days from installation to cut off
		$n = $installedAt->diffInDays($cutoff);

		//get per day and amount due
		$perDay = $internet->plan->monthly / $ndays;

		//pro rated
		$proRatedAmount = $perDay * $n;

		//get pro-rated fee id
		$fee = FeeService::ins()->findFirst('code', 'pro-rated');

		//if pro rated less than 15 days then include to next due date
		if ($n < $proRated) {
			$nextDueDate = nextDueDate($this->dueDate);

			$data = [
				'plan' => $internet->plan->name,
				'monthly' => $internet->plan->monthly,
				'installed_at' => $installedAt->format('Y-m-d'),
				'cutoff' => $cutoff->format('Y-m-d'),
				'n_days' => $n,
				'days_in_month' => $ndays,
				'per_day' => round($perDay, 2),
				'pro_rated' => round($proRatedAmount, 2)
			];

			return OtherCharge::updateOrCreate([
				'account_id' => $this->account->account_id,
				'fee_id' => $fee->fee_id,
				'due_date' => $nextDueDate
			], [
				'description' => "internet pro-rated",
				'amount' => $proRatedAmount,
				'data' => json_encode($data, JSON_UNESCAPED_SLASHES)
			]);

		} else {
			$amountDue = $proRatedAmount;
			$isProRated = 1;

			if ($n >= $ndays) {
				$amountDue = $internet->plan->monthly;
				$isProRated = 0;
			} 

			$internet->setAttribute('amount_due', $amountDue);
			$internet->setAttribute('is_pro_rated', $isProRated);

			return $internet;
		}
	}

	protected function getOtherCharges()
	{
		//add mandatory fees/ collection
		FeeService::ins()
			->findBy('other_fee', 0)
			->each(function($fee) {
				OtherCharge::updateOrCreate([
					'account_id' => $this->account->account_id,
					'fee_id' => $fee->fee_id,
					'due_date' => $this->dueDate
				], [
					'amount' => $fee->fee,
					'description' => $fee->name
				]);
			});

		//return all account charges
		return OtherChargeService::ins()
			->get([
				'account_id' => $this->account->account_id,
				'due_date' => $this->dueDate
			]);
	}

	protected function getPrevBalance()
	{
		return PaymentService::ins()
			->getModel()
			->where([
            	'account_id' => $this->account->account_id,
            	'other_payment' => 0
            ])
            ->where('due_date', '<', myCarbonize($this->dueDate)->endOfMonth())
            ->where('current_balance', '>', 0)
            ->orderBy('due_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
	}

	// penalty for non payment
	protected function getPenalty()
	{	
		$lastPayment = $this->getPrevBalance();
		if (!$lastPayment) {
			return;
		}

		$percent = dbConfig('penalty');
		$penalty = $lastPayment->current_balance * ($percent / 100); 

		$lastPayment->setAttribute('penalty', $penalty);
		$lastPayment->setAttribute('percent', $percent);

		return $lastPayment;
	}

	protected function getAdjustments()
	{
		return AdjustmentService::ins()
			->get([
				'account_id' => $this->account->account_id,
				'due_date' => $this->dueDate
			]);
	}
}