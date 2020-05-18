<?php
namespace App\Transformers;

use App\Models\WaterReading;

class WaterReadingTransformer extends AbstractTransformer
{
	protected $model = WaterReading::class;

	protected $availableIncludes = ['account'];

	public function includeAccount(WaterReading $model)
	{
		return $this->item($model->account, new AccountTransformer);
	}
}