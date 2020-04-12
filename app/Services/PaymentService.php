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
		$totalDue = $data['amount_due'];
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

		AccountService::ins()
			->getModel()
			->where('status', 'active')
			->whereRaw('accounts.account_id NOT IN(select account_id from payments WHERE due_date = ? and other_payment=0)', [$dueDate])
			->get()
			->each(function($model) use($dueDate){
				/**
				* get monthly dues
				*/
				$monthDues = MonthlyDueService::ins()->get([
					'account_id' => $model->account_id,
					'due_date' => $dueDate
				]);

				$data = [
					'account_id' => $model->account_id,
					'due_date' => $dueDate,
					'amount_due' => 0
				];

				foreach($monthDues as $monthDue) {
					switch($monthDue->code) {
						case 'adjustments':
							$data['amount_due'] -= $monthDue->amount_due;
							break;

						case 'water-bill':
						case 'internet-fee':
						case 'other-charges':
						case 'prev-balance':
						case 'penalty-non-payment':
						default:
							break;
					}

					$data['amount_due'] += $monthDue->amount_due;
				}

				$data['current_balance'] = $data['amount_due'];

				/**
				* add to payments
				* to be used/ updated by the cashier during payment
				*/
				PaymentService::ins()->add($data);
			});
	}
}