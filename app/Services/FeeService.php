<?php
namespace App\Services;

use App\Models\Fee;

class FeeService extends AbstractService
{
	protected static $class = __CLASS__;

	public function model()
	{
		return Fee::class;
	}
}