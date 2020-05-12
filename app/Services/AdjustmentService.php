<?php
namespace App\Services;

use App\Models\Adjustment;

class AdjustmentService extends AbstractService
{
	protected static $class = __CLASS__;
	
	public function model()
	{
		return Adjustment::class;
	}
}