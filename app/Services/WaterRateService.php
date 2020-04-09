<?php
namespace App\Services;

use App\Models\WaterRate;

class WaterRateService extends AbstractService
{
	protected static $class = __CLASS__;

	public function model()
	{
		return WaterRate::class;
	}
}