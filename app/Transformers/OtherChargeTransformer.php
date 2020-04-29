<?php
namespace App\Transformers;

use App\Models\OtherCharge;

class OtherChargeTransformer extends AbstractTransformer
{
	protected $model = OtherCharge::class;

	protected $availableIncludes = ['account', 'fee'];

	public function includeAccount(OtherCharge $model)
	{
		return $this->item($model->account, new AccountTransformer);
	}

	public function includeFee(OtherCharge $model)
	{
		return $this->item($model->fee, new FeeTransformer);
	}
}