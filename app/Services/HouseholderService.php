<?php
namespace App\Services;

use App\Models\{
	Householder,
	Account
};
use Carbon\Carbon;

class HouseholderService extends AbstractService
{
	protected static $class = __CLASS__;
	
	public function model()
	{
		return Householder::class;
	}
}