<?php
namespace App\Services;

use App\Models\OtherCharge;

class OtherChargeService extends AbstractService
{
	protected static $class = __CLASS__;

	public function model()
	{
		return OtherCharge::class;
	}
}