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

	public function generateOtherCharges($code, Carbon $dueDate)
	{
		AccountService::ins()
			->getModel()
			->where('status', 'active')
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