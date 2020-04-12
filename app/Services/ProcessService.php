<?php
namespace App\Services;

use App\Models\Process;

class ProcessService extends AbstractService
{
	protected static $class = __CLASS__;

	public function model()
	{
		return Process::class;
	}
}