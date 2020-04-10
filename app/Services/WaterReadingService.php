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
		/**
		* get previous reading
		*/
		$reading = $this->latest([
			'account_id' => $data['account_id'],
			'meter_no' => $data['meter_no'],
		]);

		$data['prev_read'] = $reading->prev_read ?? 0;
		$data['prev_read_date'] = $reading->prev_read_date ?? null;

		/**
		* get payment due date
		*/
		$data['due_date'] = getNextPaymentDueDate();

		// current reading date
		$data['curr_read_date'] = Carbon::now();

		//$reading = $this->add($data);
		$reading = $this->model->updateOrCreate(
			[ 'account_id' => $data['account_id'], 'due_date' => $data['due_date']->format('Y-m-d') ],
			Arr::except($data, ['account_id', 'due_date'])
		);
		if(!$reading) {
			throw new \Exception('current reading not saved.');
		}

		return $reading;
	}
}