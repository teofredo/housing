<?php
namespace App\Services;

use App\Models\WaterReading;

class WaterReadingService extends AbstractService
{
	protected static $class = __CLASS__;
	
	public function model()
	{
		return WaterReading::class;
	}
	
	public function addWaterReading(array $data)
	{
		
	}
}