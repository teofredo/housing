<?php
namespace App\Services;

use App\Models\Lot;

class LotService extends AbstractService
{
	protected static $class = __CLASS__;

	public function model()
	{
		return Lot::class;
	}
}