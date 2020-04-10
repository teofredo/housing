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
			'account_id' => (int) $model->account_id,
			'reference_no' => $model->reference_no,
			'amount_due' => $model->amount_due,
			'prev_balance' => $model->prev_balance,
			'total_due' => $model->amount_due + $model->prev_balance,
			'amount_received' => $model->amount_received,
			'amount_paid' => $model->amount_paid,
			'change' => $model->amount_received - $model->amount_paid,
			'current_balance' => $model->current_balance,
			'due_date' => $model->due_date,
			'paid_at' => $model->paid_at,
			'created_at' => $model->created_at
		];
	}
}