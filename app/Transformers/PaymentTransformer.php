<?php
namespace App\Transformers;

use App\Models\Payment;

class PaymentTransformer extends AbstractTransformer
{
	protected $model = Payment::class;

	public function transform($model)
	{
		if(!$model instanceof Payment) {
			return [];
		}

		return [
			'payment_id' => (int) $model->payment_id,
			'code' => $model->code,
			'account_id' => (int) $model->account_id,
			'soa_no' => $model->soa_no,
			'or_no' => $model->or_no,
			'amount_due' => (double) $model->amount_due,
			'amount_paid' => (double) $model->amount_paid,
			'current_balance' => (double) $model->current_balance,
			'due_date' => $model->due_date,
			'paid_at' => $model->paid_at,
			'other_payment' => $model->other_payment,
			'description' => $model->description,
			'created_at' => $model->created_at
		];
	}
}