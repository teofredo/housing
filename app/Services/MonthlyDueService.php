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

	public function model()
	{
		return MonthlyDue::class;
	}

	public function generateMonthDue($dueDate=null)
	{
		$generatorLock = dbConfig('generator-lock');
		if(!$generatorLock || $generatorLock->value == 0) {
			throw new \Exception('generator-lock must be defined and enabled in config');
		}

		$dueDate = $dueDate ? Carbon::parse($dueDate) : getNextPaymentDueDate();

		//loop through month due codes
		$codes = config('fairchild.month-due-codes');
		foreach($codes as $code) {
			$method = 'generate' . Str::studly(str_replace('-', '_', $code));
			if(method_exists($this, $method)) {
				$this->$method($code, $dueDate);
			}
		}
	}

	public function generatePenaltyNonPayment($code, Carbon $dueDate)
	{
		AccountService::ins()
            ->findBy('status', 'active')
            ->each(function($model) use($dueDate){
                /**
                * get last payment history
                */
                $payment = PaymentService::ins()
                    ->getModel()
                    ->where('account_id', $model->account_id)
                    ->orderBy('due_date', 'desc')
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
	                	->where([
	                		'account_id' => $model->account_id,
	                		'due_date' => $dueDate
	                	])
	                	->whereNotIn('code', ['adjustments']);

	                //add penalty to original amount
	                $amountDue = $monhthDues->sum('amount_due');
	                $percent = (double) dbConfig('penalty')->value;
	                $penalty = $amountDue * ($penalty/100);
	                $adWithPenalty = $amountDue + $penalty;

	                $data = [
	                	'last_payment' => $payment->toJson(),
	                	'month_dues' => $monthDues->get()->toJson(),
	                	'amount_due' => $amountDue,
	                	'penalty' => $penalty,
	                	'ad_with_penalty' => $adWithPenalty
	                ];

	            	$this->model->updateOrCreate(
						['code' => $code, 'account_id' => $model->account_id, 'due_date' => $dueDate],
						['amount_due' => $penalty, 'data' => json_encode($data)]
					);
	            }

            });
	}

	public function generatePrevBalance($code, Carbon $dueDate)
	{
		PaymentService::ins()
			->getModel()
			->where('due_date', $dueDate->copy()->subMonthNoOverflow())
			->where('current_balance', '>', 0)
			->get()
			->each(function($model) use($code, $dueDate){
				$this->model->updateOrCreate(
					['code' => $code, 'account_id' => $model->account_id, 'due_date' => $dueDate],
					['amount_due' => $model->current_balance, 'data' => $model->toJson()]
				);
			});
	}

	public function generateOtherCharges($code, Carbon $dueDate)
	{
		/**
		* delete non other fees, mandatory fees
		*/
		OtherChargeService::ins()
			->getModel()
			->where('due_date', $dueDate)
			->join('fees', function($join){
				$join->on('other_charges.fee_id', '=', 'fees.fee_id')
					->where('fees.other_fee', 0);
			})
			->delete();

		$accounts = AccountService::ins()->findBy('status', 'active');
		$fees = FeeService::ins()->findBy('other_fee', 0);

		/**
		* Add fees to account, insert to other_charges
		*/
		$accounts
			->get()
			->each(function($model) use($fees, $dueDate){
				$fees->each(function($fee) use($model, $dueDate){
					OtherChargeService::ins()->add([
						'account_id' => $model->account_id,
						'fee_id' => $fee->fee_id,
						'description' => $fee->name,
						'amount' => $fee->fee,
						'due_date' => $dueDate
					]);
				});
			});

		/**
		* add to month due
		*/
		$accounts
			->with(['otherCharges' => function($q) use($dueDate){
				$q->where('due_date', $dueDate);
			}])
			->get()
			->each(function($model) use($code, $dueDate){
				$amountDue = $model->otherCharges->sum('amount');

				$data = [
					'charges' => $model->otherCharges->toJson(),
					'code' => $code,
					'amount_due' => $amountDue,
				];

				$this->model->updateOrCreate(
					['code' => $code, 'account_id' => $model->account_id, 'due_date' => $dueDate],
					['amount_due' => $amountDue, 'data' => json_encode($data)]
				);
			});
	}

	public function generateInternetFee($code, Carbon $dueDate)
	{
		InternetSubscriptionService::ins()
			->get([
				'active' => 1,
				'installed' => 1
			])
			->each(function($model) use($code, $dueDate){
				$model->installed_at = Carbon::parse($model->installed_at);
				$currentMonth = $dueDate->format('Y-m');
				$lastMonth = $dueDate
					->copy()
					->subMonthNoOverflow()
					->format('Y-m');
				$monthInstalled = $model->installed_at->format('Y-m');

				$cutOff = dbConfig('cut-off')->value;
				$proRatedDays = (int) dbConfig('pro-rated')->value;

				$currentCutOff = $dueDate->copy()->day($cutOff);
				$lastCutOff = $currentCutOff->copy()->subMonthNoOverflow();
				$lastDueDate = $dueDate->copy()->subMonthNoOverflow();

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
							$due_date = $dueDate;
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

					$daysInMonth = (int)$dueDate
						->copy()
						->endOfMonth()
						->format('d');

					$perDay = (double)$model->plan->monthly / $daysInMonth;

					//no of days from installation to current cut-off
					$diffInDaysToCurrentCutOff = $model->installed_at->diffInDays($currentCutOff);
					$currentProRated = $diffInDaysToCurrentCutOff * $perDay;
					
					$proRatedFrom = $model->installed_at->format('m/d/y');
					$proRatedTo = $currentCutOff->format('m/d/y');

					$due_date = $dueDate;
					if($diffInDaysToCurrentCutOff < $proRatedDays) {
						$due_date = $dueDate->copy()->addMonthNoOverflow();
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
						'plan' => $model->plan->toJson(),
						'subscription' => $model->toJson(),
						'amount_due' => $model->plan->monthly
					];

					$this->model->updateOrCreate(
						['code' => $code, 'account_id' => $model->account_id, 'due_date' => $dueDate],
						['amount_due' => $model->plan->monthly, 'data' => json_encode($data)]
					);
				}
			});
	}

	public function generateWaterBill($code, Carbon $dueDate)
	{
		WaterReadingService::ins()
			->findBy('due_date', $dueDate)
			->each(function($model) use($code, $dueDate){
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
					'reading' => $model->toJson(),
					'rate' => $waterRate->toJson(),
					'consumption' => $consumption,
					'amount_due' => $amountDue
				];

				$this->model->updateOrCreate(
					['code' => $code, 'account_id' => $model->account_id, 'due_date' => $dueDate],
					['amount_due' => $amountDue, 'data' => json_encode($data)]
				);
			});
	}
}