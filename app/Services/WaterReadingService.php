<?php
namespace App\Services;

use App\Models\WaterReading;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\{
	Str,
	Arr
};
use Carbon\Carbon;

class WaterReadingService extends AbstractService
{
	protected static $class = __CLASS__;
	
	public function model()
	{
		return WaterReading::class;
	}
	
	public function addWaterReading(array $data)
	{
		// get previous reading
		$reading = $this->latest([
			'account_id' => $data['account_id'],
			'meter_no' => $data['meter_no'],
		]);

		$data['prev_read'] = $reading->curr_read ?? 0;
		$data['prev_read_date'] = $reading->curr_read_date ?? null;
		$data['due_date'] = $data['due_date'] ?? getDueDate();

		//get water rate
		$consumption = $data['curr_read'] - $data['prev_read'];
		$waterRate = $this->getWaterRateByConsumption($consumption);
		$data['rate_applied'] = $waterRate->rate ?? 0;
		$data['is_minimum'] = $waterRate->is_minimum ?? 0;

		// current reading date
		$data['curr_read_date'] = Carbon::now();

		$reading = $this->model->updateOrCreate(
			[ 'account_id' => $data['account_id'], 'due_date' => $data['due_date'] ],
			Arr::except($data, ['account_id', 'due_date'])
		);
		if(!$reading) {
			throw new \Exception('current reading not saved.');
		}

		return $reading;
	}

	public function getWaterRateByConsumption($consumption)
	{
		$waterRate = WaterRateService::ins()
			->getModel()
			->where('min_m3', '<=', $consumption)
			->where('max_m3', '>=', $consumption)
			->first();

		if(!$waterRate) {
			return null;
		}

		if($waterRate->min_fee > 0) {
			return (object) array_merge($waterRate->toArray(), [
				'rate' => $waterRate->min_fee,
				'is_minimum' => true
			]);
		}

		return (object) array_merge($waterRate->toArray(), [
			'rate' => $waterRate->per_m3,
			'is_minimum' => false
		]);
	}
}