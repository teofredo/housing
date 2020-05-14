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

		//if sufficient amount receivedice
		if($difference <= 0) {
			$data['amount_paid'] = $totalDue;
			$data['current_balance'] = 0;
		} else {
			$data['amount_paid'] = $data['amount_received'];
			$data['current_balance'] = $difference;
		}

		return $this->add($data);
	}


	public function initPayments($dueDate=null)
	{
		$dueDate = $dueDate instanceof Carbon ? $dueDate : getDueDate();

		$monthDue = MonthlyDueService::ins()->findFirst('due_date', $dueDate);
		if(!$monthDue) {
			throw new \Exception('month due not yet generated');
		}

		AccountService::ins()
			->getModel()
			->where('status', 'active')
			->whereRaw('accounts.account_id NOT IN(select account_id from payments WHERE due_date = ? and other_payment = ?)', [$dueDate, 0])
			->get()
			->each(function($model) use($dueDate){
				$data = [
					'account_id' => $model->account_id,
					'due_date' => $dueDate,
					'amount_due' => 0
				];

				// get month dues
				$monthDues = MonthlyDueService::ins()->get([
					'account_id' => $model->account_id,
					'due_date' => $dueDate
				]);

				$penalty = 0;
				foreach($monthDues as $m) {
					switch($m->code) {
						case 'adjustments':
							$data['amount_due'] -= $m->amount_due;
							continue;

						case 'water':
						case 'internet':
						case 'other_charges':
						case 'prev_balance':
							$data['amount_due'] += $m->amount_due;
							continue;

						case 'penalty':
							$penalty += $m->amount_due;
							continue;
					}
				}

				$data['current_balance'] = $data['amount_due'] + $penalty;

				PaymentService::ins()->add($data);
			});
	}
}