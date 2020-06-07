<?php
namespace App\Transformers;

use App\Models\Fee;

class FeeTransformer extends AbstractTransformer
{
	protected $model = Fee::class;
	
	public function transform($model)
	{
		if(!$model instanceof Fee) {
			return [];
		}
		
		return [
			'fee_id' => (int) $model->fee_id,
			'code' => $model->code,
			'name' => $model->name,
			'fee' => (double) $model->fee,
			'other_fee' => (bool) $model->other_fee,
			'description' => $model->description,
			'deleble' => (bool) $model->deleble,
			'created_at' => $model->created_at
		];
	}
}