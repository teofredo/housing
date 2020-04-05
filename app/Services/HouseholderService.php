<?php
namespace App\Services;

use App\Models\{
	Householder,
	Account
};
use Carbon\Carbon;

class HouseholderService extends AbstractService
{
	public function model()
	{
		return Householder::class;
	}
}