<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\{
	Arr,
	Str
};
use App\Models\{
	MonthlyDue,
	WaterReading,
	OtherCharge,
	InternetSubscription,
	Account
};
use App\Traits\AccountSummary;
use Illuminate\Support\Facades\DB;

class MonthlyDueService extends AbstractService
{
	use AccountSummary;

	protected static $class = __CLASS__;

	protected $account;

	protected $dueDate;

	public function model()
	{
		return MonthlyDue::class;
	}

	public function getSummary($dueDate)
	{
		return $this->model
			->where('due_date', $dueDate)
			->where('code', '<>', 'adjustments')
			->join('accounts', 'accounts.account_id', '=', 'monthly_dues.account_id')
			->select(['amount_due' => function($query) {
				$query->selectRaw('sum(amount_due) - (SELECT SUM(amount_due) FROM monthly_dues WHERE code = ? AND account_id = accounts.account_id AND due_date = monthly_dues.due_date GROUP BY account_id, due_date)', ['adjustments']);
			}])
			->addSelect(
				'accounts.account_id as account_id', 
				'account_no',
				'account_name',
				'due_date'
			)
			->groupBy('account_id')
			->groupBy('due_date')
			->get();
	}

	public function generateMonthDue($dueDate)
	{
		$this->dueDate = $dueDate;

		Account::all()->each(function($account) {
			$summary = $this->summarize($account);

			foreach($summary as $key => $value) {
				$fn = 'save' . Str::studly($key);
				
				if (method_exists($this, $fn) 
					&& is_callable([$this, $fn])) {

					$this->$fn($value);
				}
			}
		});
	}

	private function saveWater($model)
	{
		$this->commit([
			'code' => 'water',
			'amount_due' => $model->rate_applied ?? 0,
			'data' => $model ? $model->toJson() : null
		]);
	}

	private function saveInternet($model)
	{
		$data = [];

		if ($model instanceof OtherCharge) {
			$data = [
				'amount_due' => 0,
				'data' => $model->toJson()
			];
		} elseif ($model instanceof InternetSubscription) {
			$data = [
				'amount_due' => $model->amount_due ?? 0,
				'data' => $model ? $model->toJson() : null
			];
		}

		$data['code'] = 'internet';

		$this->commit($data);
	}

	private function saveOtherCharges($collection)
	{
		$this->commit([
			'code' => 'other_charges',
			'amount_due' => $collection ? $collection->sum('amount') : 0,
			'data' => $collection ? $collection->toJson() : null
		]);
	}

	private function savePrevBalance($model)
	{
		$this->commit([
			'code' => 'prev_balance',
			'amount_due' => $model->current_balance ?? 0,
			'data' => $model ? $model->toJson() : null
		]);
	}

	private function savePenalty($model)
	{
		$this->commit([
			'code' => 'penalty',
			'amount_due' => $model->penalty ?? 0,
			'data' => $model ? $model->toJson() : null
		]);	
	}

	private function saveAdjustments($collection)
	{
		$this->commit([
			'code' => 'adjustments',
			'amount_due' => $collection ? $collection->sum('amount') : 0,
			'data' => $collection ? $collection->toJson() : null
		]);	
	}

	private function commit(array $data)
	{
		MonthlyDue::updateOrCreate([
			'code' => $data['code'],
			'account_id' => $this->account->account_id,
			'due_date' => $this->dueDate
		], [
			'amount_due' => $data['amount_due'] ?? 0,
			'data' => $data['data'] ?? null
		]);	
	}
}