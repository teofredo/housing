<?php
namespace App\Transformers;

use App\Models\MonthlyDue;

class MonthlyDueTransformer extends AbstractTransformer
{
	protected $model = MonthlyDue::class;

	public function transform($model)
	{
		if (!$model instanceof MonthlyDue) {
			return [];
		}

		return [
			'id' => (int) $model->id,
			'code' => $model->code,
			'account_id' => (int) $model->id,
			'due_date' => $model->due_date,
			'amount_due' => (double) $model->amount_due,
			'data' => json_decode($model->data),
			'created_at' => $model->created_at
		];
	}
}