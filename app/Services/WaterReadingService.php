<?php
namespace App\Services;

use App\Models\{
	WaterReading,
	WaterRate
};
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
	
	public function saveWaterReading(array $data, $id=null)
	{
		// get previous reading
		$reading = WaterReading::where([
			'account_id' => $data['account_id'],
			'meter_no' => $data['meter_no']
		]);
		
		// for editing
		if ($id) {
			$reading = $reading->where('id', '<>', $id);
		}
		
		$reading = $reading->first();

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
		
		$reading = null;
		if (!$id) {
			$reading = $this->model->create($data);
		} else {
			if ($success = $this->model->where('id', $id)->update($data)) {
				$reading = WaterReading::find($id);
			}
		}
		
		if(!$reading) {
			throw new \Exception('save failed.');
		}

		return $reading;
	}

	public function getWaterRateByConsumption($consumption)
	{
		$waterRate = WaterRate::whereRaw('? BETWEEN min_m3 AND max_m3', [$consumption])->first();
		if(!$waterRate) {
			throw new \Exception('No applicable rates for this reading.');
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