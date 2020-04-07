<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\{
	MonthlyDue,
	WaterRate,
	WaterReading
};

class MonthlyDueService extends AbstractService
{
	protected static $class = __CLASS__;

	private $dueDate;

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

		$this->dueDate = $dueDate ? Carbon::parse($dueDate) : getNextPaymentDueDate();

		//loop through month due codes
		$codes = config('fairchild.month-due-codes');
		foreach($codes as $code) {
			switch($code) {
				case 'water-bill':
					$this->createWaterBill();
					break;
			}
		}
	}

	private function createWaterBill()
	{
		// $reading = WaterReading::where();
	}
}