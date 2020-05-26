<?php
namespace App\Services;

use App\Models\Soa;

class SoaService extends AbstractService
{
	protected static $class = __CLASS__;

	public function model()
	{
		return Soa::class;
	}
}