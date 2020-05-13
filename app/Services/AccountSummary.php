<?php
namespace App\Services;

use App\Models\{
	Account,
	OtherCharge
};
use Carbon\Carbon;
use Illuminate\Support\Str;

class AccountSummary
{
	private $account;

	private $dueDate;

	private $interceptor = true;

	public function __construct($account = null)
	{
		$this->dueDate = getDueDate();
		// dd($this->dueDate, $account);

		if($account) {
			$this->setAccount($account);
		}
	}

	public function setAccount(Account $account)
	{
		if($account->status != 'active') {
			throw new \Exception('invalid account');
		}

		$this->account = $account;
		return $this;
	}

	/**
	* dueDate override
	* for testing purposes only
	*/
	public function setDueDate(Carbon $dueDate)
	{
		$this->dueDate = $dueDate;
		return $this;
	}

	public function getWaterBill()
	{
		return WaterReadingService::ins()->first([
			'account_id' => $this->account->account_id,
			'due_date' => $this->dueDate
		]);
	}

	public function getInternetBill()
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
			$nextDueDate = getNextDueDate(
				$this->dueDate->copy()->addMonthNoOverflow()
			);

			$data = [
				'plan' => $internet->plan->name,
				'monthly' => $internet->plan->monthly,
				'installed_at' => $installedAt->format('m/d/Y'),
				'cutoff' => $cutoff->format('m/d/Y'),
				'n_days' => $n,
				'days_in_month' => $ndays,
				'per_day' => round($perDay, 2),
				'pro_rated' => round($proRatedAmount, 2)
			];

			return OtherCharge::updateOrCreate([
				'account_id' => $this->account->account_id,
				'fee_id' => $fee->fee_id,
				'due_date' => $nextDueDate->format('Y-m-d')
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

	public function getOtherCharges()
	{
		//add mandatory fees/ collection
		FeeService::ins()
			->findBy('other_fee', 0)
			->each(function($fee) {
				OtherCharge::updateOrCreate([
					'account_id' => $this->account->account_id,
					'fee_id' => $fee->fee_id,
					'due_date' => $this->dueDate->format('Y-m-d')
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

	public function getPrevBalance()
	{
		return PaymentService::ins()
			->getModel()
			->where([
            	'account_id' => $this->account->account_id,
            	'other_payment' => 0
            ])
            ->where('due_date', '<', $this->dueDate)
            ->where('current_balance', '>', 0)
            ->orderBy('due_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
	}

	/**
	* penalty for non payment
	*/
	public function getPenalty()
	{	
		$lastPayment = $this->getPrevBalance();
		if (!$lastPayment) {
			return;
		}

		$percent = dbConfig('penalty-non-payment');
		return $lastPayment->current_balance * ($percent / 100); 
	}

	public function getAdjustments()
	{
		return AdjustmentService::ins()
			->get([
				'account_id' => $this->account->account_id,
				'due_date' => $this->dueDate
			]);
	}

	public function __get($fn)
	{
		$fn = 'get' . Str::studly($fn);
		if (method_exists($this, $fn)) {
			$response = $this->$fn();

			/**
			* response interceptor
			*/
			if ($this->interceptor
				&& (is_subclass_of($response, \Illuminate\Database\Eloquent\Model::class)
				|| $response instanceof \Illuminate\Database\Eloquent\Collection)) {
				
				return $response->toArray();
			}

			return $response;
		}
	}

	public function setInterceptor(bool $value)
	{
		$this->interceptor = $value;
	}
}