<?php
namespace App\Services;

use App\Models\Payment;

class PaymentService extends AbstractService
{
	protected static $class = __CLASS__;

	public function model()
	{
		return Payment::class;
	}

	public function addPayment(array $data)
	{
		$totalDue = $data['amount_due'] + $data['prev_balance'];
		$difference = $totalDue - $data['amount_received'];

		//if sufficient amount received
		if($difference <= 0) {
			$data['amount_paid'] = $totalDue;
			$data['current_balance'] = 0;
		} else {
			$data['amount_paid'] = $data['amount_received'];
			$data['current_balance'] = $difference;
		}

		return $this->add($data);
	}
}