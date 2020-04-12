<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\{
	Arr,
	Str
};
use App\Models\{
	MonthlyDue
};

class MonthlyDueService extends AbstractService
{
	protected static $class = __CLASS__;

	private $dueDate;

	public function model()
	{
		return MonthlyDue::class;
	}

	public function checkGeneratorLock()
	{
		$generatorLock = dbConfig('generator-lock');
		if(!$generatorLock || $generatorLock->value == 0) {
			throw new \Exception('generator-lock must be defined and enabled in config');
		}

		return $this;
	}

	public function setDueDate(Carbon $dueDate)
	{
		$this->dueDate = $dueDate;
		return $this;
	}

	public function generateAdjustments()
	{
		AccountService::ins()
			->getModel()
            ->where('status', 'active')
            ->with(['adjustments' => function($q){
            	$q->where('due_date', $this->dueDate);
            }])
            ->get()
            ->each(function($model){
            	if(!$model->adjustments->toArray()) {
            		return true; // continue
            	}

            	$amountDue = $model->adjustments->sum('amount');

            	$data = [
            		'amount_due' => $amountDue,
            		'adjustments' => $model->adjustments->toArray()
            	];

            	$this->model->updateOrCreate(
					['code' => 'adjustments', 'account_id' => $model->account_id, 'due_date' => $this->dueDate],
					['amount_due' => $amountDue, 'data' => json_encode($data, JSON_UNESCAPED_SLASHES)]
				);
        	});

        return $this;
	}

	public function generatePenaltyForNonPayment()
	{
		AccountService::ins()
            ->findBy('status', 'active')
            ->each(function($model){
                /**
                * get last payment history
                */
                $payment = PaymentService::ins()
                    ->getModel()
                    ->where([
                    	'account_id' => $model->account_id,
                    	'other_payment' => 0
                    ])
                    ->orderBy('due_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if(!$payment) {
                	return true; // continue
                }

                /**
                * add penalty for non payment
                */
                if(!$payment->paid_at) { 

                	// get total amount due for the month
	                $monthDues = MonthlyDueService::ins()
	                	->getModel()
	                	->where([
	                		'account_id' => $model->account_id,
	                		'due_date' => $this->dueDate
	                	])
	                	->whereNotIn('code', ['penalty-non-payment', 'adjustments']);

	                //add penalty to original amount
	                $amountDue = $monthDues->sum('amount_due');
	                $percent = (double) dbConfig('penalty-non-payment')->value;
	                $penalty = $amountDue * ($percent/100);
	                $adWithPenalty = $amountDue + $penalty;

	                $data = [
	                	'amount_due' => $amountDue,
	                	'penalty' => $penalty,
	                	'ad_with_penalty' => $adWithPenalty,
	                	'last_payment' => $payment->toArray(),
	                	'month_dues' => $monthDues
	                		->select('id','code','account_id','due_date','amount_due')
	                		->get()
	                		->toArray()
	                ];

	            	$this->model->updateOrCreate(
						['code' => 'penalty-non-payment', 'account_id' => $model->account_id, 'due_date' => $this->dueDate],
						['amount_due' => $penalty, 'data' => json_encode($data, JSON_UNESCAPED_SLASHES)]
					);
	            }
            });

        return $this;
	}

	public function generatePreviousBalance()
	{
		AccountService::ins()
            ->findBy('status', 'active')
            ->each(function($model){

			$payment = PaymentService::ins()
                ->getModel()
                ->where([
                	'account_id' => $model->account_id,
                	'other_payment' => 0
                ])
                ->orderBy('due_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            if(!$payment) {
            	return true;
            }

            $this->model->updateOrCreate(
				['code' => 'prev-balance', 'account_id' => $model->account_id, 'due_date' => $this->dueDate],
				['amount_due' => $payment->current_balance, 'data' => $payment->toJson()]
			);
		});

        return $this;
	}

	public function generateOtherCharges()
	{
		/**
		* delete non other fees, mandatory fees
		*/
		OtherChargeService::ins()
			->getModel()
			->where('due_date', $this->dueDate)
			->join('fees', function($join){
				$join->on('other_charges.fee_id', '=', 'fees.fee_id')
					->where('fees.other_fee', 0);
			})
			->forceDelete();

		$accounts = AccountService::ins()
			->getModel()
			->where('status', 'active');

		$fees = FeeService::ins()->findBy('other_fee', 0);

		/**
		* Add fees to account, insert to other_charges
		*/
		$accounts
			->get()
			->each(function($model) use($fees){
				$fees->each(function($fee) use($model){
					OtherChargeService::ins()->add([
						'account_id' => $model->account_id,
						'fee_id' => $fee->fee_id,
						'description' => $fee->name,
						'amount' => $fee->fee,
						'due_date' => $this->dueDate
					]);
				});
			});

		/**
		* add to month due
		*/
		$accounts
			->with(['otherCharges' => function($q){
				$q->where('due_date', $this->dueDate);
			}])
			->get()
			->each(function($model){
				if(!$model->otherCharges->toArray()) {
            		return true; // continue
            	}

				$amountDue = $model->otherCharges->sum('amount');

				$data = [
					'charges' => $model->otherCharges->toArray(),
					'amount_due' => $amountDue,
				];

				$this->model->updateOrCreate(
					['code' => 'other-charges', 'account_id' => $model->account_id, 'due_date' => $this->dueDate],
					['amount_due' => $amountDue, 'data' => json_encode($data, JSON_UNESCAPED_SLASHES)]
				);
			});

		return $this;
	}

	public function generateInternetFee()
	{
		InternetSubscriptionService::ins()
			->get([
				'active' => 1,
				'installed' => 1
			])
			->each(function($model){
				$model->installed_at = Carbon::parse($model->installed_at);
				$currentMonth = $this->dueDate->format('Y-m');
				$lastMonth = $this->dueDate
					->copy()
					->subMonthNoOverflow()
					->format('Y-m');
				$monthInstalled = $model->installed_at->format('Y-m');

				$cutOff = dbConfig('cut-off')->value;
				$proRatedDays = (int) dbConfig('pro-rated')->value;

				$currentCutOff = $this->dueDate->copy()->day($cutOff);
				$lastCutOff = $currentCutOff->copy()->subMonthNoOverflow();
				$lastDueDate = $this->dueDate->copy()->subMonthNoOverflow();

				if(!$currentCutOff->isValid() || !$lastCutOff->isValid()) {
					throw new \Exception('cut off date is not valid');
				}

				/**
				* if installed last month and before cut-off
				*/
				if($monthInstalled == $lastMonth 
					&& $model->installed_at->lte($lastCutOff)) {
					
					//no of days in the month when installed
					$daysInMonth = (int)$model->installed_at
						->copy()
						->endOfMonth()
						->format('d');

					$perDay = (double)$model->plan->monthly / $daysInMonth;

					if($model->installed_at->lte($lastCutOff)) {

						//no of days from installation to last cut-off
						$diffInDaysToLastCutOff = $model->installed_at->diffInDays($lastCutOff);
						$lastProRated = $diffInDaysToLastCutOff * $perDay;

						$proRatedFrom = $model->installed_at->format('m/d/y');
						$proRatedTo = $lastCutOff->format('m/d/y');

						/**
						* if no of days from installation to cut-off < 15days
						* then collect on next month due
						*/
						$due_date = $lastDueDate;
						if($diffInDaysToLastCutOff < $proRatedDays) {
							$due_date = $this->dueDate;
						}

						$fee = FeeService::ins()->findFirst('code', 'other-fee');

						$otherCharge = OtherChargeService::ins()->add([
							'account_id' => $model->account_id,
							'fee_id' => $fee->fee_id,
							'description' => "pro-rated from {$proRatedFrom} to {$proRatedTo}",
							'amount' => $lastProRated,
							'due_date' => $due_date
						]);
					}

				/**
				* if installed last month and after cut-off or installed in current month
				*/
				} else if(($monthInstalled == $lastMonth
					&& $model->installed_at->gt($lastCutOff))
					|| $monthInstalled == $currentMonth) {

					$daysInMonth = (int)$this->dueDate
						->copy()
						->endOfMonth()
						->format('d');

					$perDay = (double)$model->plan->monthly / $daysInMonth;

					//no of days from installation to current cut-off
					$diffInDaysToCurrentCutOff = $model->installed_at->diffInDays($currentCutOff);
					$currentProRated = $diffInDaysToCurrentCutOff * $perDay;
					
					$proRatedFrom = $model->installed_at->format('m/d/y');
					$proRatedTo = $currentCutOff->format('m/d/y');

					$due_date = $this->dueDate;
					if($diffInDaysToCurrentCutOff < $proRatedDays) {
						$due_date = $this->dueDate->copy()->addMonthNoOverflow();
					}

					$fee = FeeService::ins()->findFirst('code', 'other-fee');

					$otherCharge = OtherChargeService::ins()->add([
						'account_id' => $model->account_id,
						'fee_id' => $fee->fee_id,
						'description' => "pro-rated from {$proRatedFrom} to {$proRatedTo}",
						'amount' => $currentProRated,
						'due_date' => $due_date
					]);

				/**
				* if installed 2 months ago and beyond
				*/
				} else if($model->installed_at->lt($lastDueDate->copy()->startOfMonth())) {
					$data = [
						'plan' => $model->plan->toArray(),
						'subscription' => $model->toArray(),
						'amount_due' => $model->plan->monthly
					];

					$this->model->updateOrCreate(
						['code' => 'internet-fee', 'account_id' => $model->account_id, 'due_date' => $this->dueDate],
						['amount_due' => $model->plan->monthly, 'data' => json_encode($data, JSON_UNESCAPED_SLASHES)]
					);
				}
			});

		return $this;
	}

	public function generateWaterBill()
	{
		WaterReadingService::ins()
			->findBy('due_date', $this->dueDate)
			->each(function($model){
				$consumption = $model->curr_read - $model->prev_read;
				
				$waterRate = WaterRateService::ins()
					->getModel()
					->where('min_m3', '<=', $consumption)
					->where('max_m3', '>=', $consumption)
					->first();

				if(!$waterRate) {
					throw new \Exception('water rate not set');
				}

				$amountDue = ($consumption * $waterRate->per_m3) ?: (double)$waterRate->min_fee;

				$data = [
					'reading' => $model->toArray(),
					'rate' => $waterRate->toArray(),
					'consumption' => $consumption,
					'amount_due' => $amountDue
				];

				$this->model->updateOrCreate(
					['code' => 'water-bill', 'account_id' => $model->account_id, 'due_date' => $this->dueDate],
					['amount_due' => $amountDue, 'data' => json_encode($data, JSON_UNESCAPED_SLASHES)]
				);
			});

		return $this;
	}
}