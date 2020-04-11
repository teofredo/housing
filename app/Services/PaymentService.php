<?php
namespace App\Services;

use App\Models\Payment;
use Carbon\Carbon;

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


	public function initPayments(Carbon $dueDate)
	{
		$monthDue = MonthlyDueService::ins()->findFirst('due_date', $dueDate);
		if(!$monthDue) {
			throw new \Exception('month due not yet generated');
		}

		// AccountService::ins()
		// 	->getModel()
		// 	->where('status', 'active')
		// 	->whereRaw('accounts.account_id NOT IN(select account_id from payments WHERE due_date = ?)', [$dueDate])
		// 	->get()
		// 	->each(function($model) use($dueDate){
		// 		/**
		// 		* get monthly dues
		// 		*/
		// 		$monthDues = MonthlyDueService::ins()->get([
		// 			'account_id' => $model->account_id,
		// 			'due_date' => $dueDate
		// 		]);

		// 		$totalAmountDue = 0;

		// 		foreach($monthDues as $monthDue) {
		// 			if($monthDue->code == 'adjustments') {
		// 				$totalAmountDue -= $monthDue->amount_due;
		// 				continue;
		// 			}

		// 			$totalAmountDue += $monthDue->amount_due;
		// 		}

		// 		/**
		// 		* last payment history
		// 		*/
		// 		$payment = PaymentService::ins()
  //                   ->getModel()
  //                   ->where('account_id', $model->account_id)
  //                   ->orderBy('due_date', 'desc')
  //                   ->first();

		// 		/**
		// 		* add to payments
		// 		* to be used/ updated by the cashier during payment
		// 		*/
		// 		PaymentService::ins()->add([
		// 			'account_id' => $model->account_id,
		// 			'amount_due' => $totalAmountDue,
		// 			'prev_balance' => $payment->current_balance ?? 0,
		// 			// 'current_balance' => 
		// 			'due_date' => $dueDate
		// 		]);
		// 	});


		//REFER TO MONTHLY_DUES TABLE
	}
}