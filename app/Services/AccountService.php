<?php
namespace App\Services;

use App\Models\{
	Account,
	Householder,
	User,
	OtherCharge
};
use Carbon\Carbon;
use Illuminate\Support\Arr;

class AccountService extends AbstractService
{
	protected static $class = __CLASS__;
	
	public function model()
	{
		return Account::class;
	}
	
	public function createAccount(array $data=[])
	{
		/**
		* build account data
		*/
		$data['username'] = $data['email'];
		$data['password'] = isset($data['password']) ? bcrypt($data['password']) : null;
		
		//build account_name
		$middlename = $data['middlename'] ?? null;
		$mi = $middlename ? "{$middlename[0]}." : '';
		$suffix = $data['suffix'] ?? null;
		$data['account_name'] = "{$data['firstname']} {$mi} {$data['lastname']} {$suffix}";
		
		//others
		$data['parent_id'] = $data['parent_id'] ?? null;
		
		//create account
		$account = $this->model->create(Arr::only($data, ['parent_id', 'account_name', 'email', 'username', 'password']));
		if(!$account) {
			throw new \Exception('failed to create account');
		}
		
		//build account_no and save
		$year = Carbon::now()->format('Y');
		$parentId = substr(sprintf('%04s', $account->parent_id), -4);
		$accountId = substr(sprintf('%04s', $account->account_id), -4);
		$hhti = strtoupper($data['householder_type'][0]);	//householder_type initial
		$account->account_no = "{$parentId}{$year}{$accountId}{$hhti}";
		$account->save();
		
		/**
		* build householder data
		*/
		$data = array_merge(Arr::only($data, ['block_id', 'lot_id']), [
			'account_id' => $account->account_id,
			'house_no' => $data['house_no'] ?? null,
			'water_meter_no' => $data['water_meter_no'] ?? null,
			'type' => $data['householder_type'],
			'contact_no' => json_encode($data['contact_no']),
			'name' => json_encode([
				'first' => $data['firstname'],
				'last' => $data['lastname'],
				'middle' => $middlename,
				'suffix' => $suffix
			]),
			'moved_in' => $data['moved_in'] ?? null
		]);
		
		//create householder
		$householder = Householder::create($data);		
		if(!$householder) {
			throw new \Exception('failed to add householder info');
		}

		//create householder house_no and water_meter_no
		$houseNo = "{$householder->lot->block->name}{$householder->lot->name}";
		$householder->house_no = $houseNo;
		$householder->water_meter_no = str_rot13($houseNo);
		$householder->save();
		
		//todo > job worker > send email verification link with reset password
		//

		return $account;
	}

	public function setAccount(Account $account)
	{
		if($account->status != 'active') {
			throw new \Exception('invalid account');
		}

		$this->account = $account;
		return $this;
	}

	public function setDueDate(Carbon $dueDate)
	{
		$this->dueDate = $dueDate;
		return $this;
	}

	public function waterBill()
	{
		return WaterReadingService::ins()->first([
			'account_id' => $this->account->account_id,
			'due_date' => $this->dueDate
		]);
	}

	public function internetBill()
	{
		$internet = InternetSubscriptionService::ins()
			->getModel()
			->where([
				'account_id' => $this->account->account_id,
				'active' => 1,
			])
			->whereNotNull('installed_at')
			->first();

		if (!$internet) {
			return $internet;
		};
		
		$installedAt = Carbon::parse($internet->installed_at);
		$cutoff = getCutoff();
		$prevCutoff = $cutoff->copy()->subMonthNoOverflow();

		//no of days considered as pro rated
		$proRated = (int) dbConfig('pro-rated');

		//no of days from prev to current cutoff
		$ndays = $cutoff->diffInDays($prevCutoff);

		//no of days from installation to cut off
		$n = $installedAt->diffInDays($cutoff);

		//get per day and amount due
		$perDay = $internet->plan->monthly / $ndays;

		//pro rated
		$proRatedAmount = $perDay * $n;

		//get pro-rated fee id
		$fee = FeeService::ins()->findFirst('code', 'pro-rated');

		//if pro rated less than 15 days then include to next due date
		if ($n < $proRated) {
			$nextDueDate = getNextDueDate(
				$this->dueDate->copy()->addMonthNoOverflow()
			);

			$data = [
				'plan' => $internet->plan->name,
				'monthly' => $internet->plan->monthly,
				'installed_at' => $installedAt->format('m/d/Y'),
				'cutoff' => $cutoff->format('m/d/Y'),
				'n_days' => $n,
				'days_in_month' => $ndays,
				'per_day' => round($perDay, 2),
				'pro_rated' => round($proRatedAmount, 2)
			];

			return OtherCharge::updateOrCreate([
				'account_id' => $this->account->account_id,
				'fee_id' => $fee->fee_id,
				'due_date' => $nextDueDate->format('Y-m-d')
			], [
				'description' => "internet pro-rated",
				'amount' => $proRatedAmount,
				'data' => json_encode($data, JSON_UNESCAPED_SLASHES)
			]);

		} else {
			$amountDue = $proRatedAmount;
			$isProRated = 1;

			if ($n >= $ndays) {
				$amountDue = $internet->plan->monthly;
				$isProRated = 0;
			} 

			$internet->setAttribute('amount_due', $amountDue);
			$internet->setAttribute('is_pro_rated', $isProRated);

			return $internet;
		}
	}

	public function otherCharges()
	{
		//add mandatory fees/ collection
		FeeService::ins()
			->findBy('other_fee', 0)
			->each(function($fee) {
				OtherCharge::updateOrCreate([
					'account_id' => $this->account->account_id,
					'fee_id' => $fee->fee_id,
					'due_date' => $this->dueDate->format('Y-m-d')
				], [
					'amount' => $fee->fee,
					'description' => $fee->name
				]);
			});

		//return all account charges
		return OtherChargeService::ins()
			->get([
				'account_id' => $this->account->account_id,
				'due_date' => $this->dueDate
			]);
	}

	public function prevBalance()
	{
		return PaymentService::ins()
			->getModel()
			->where([
            	'account_id' => $this->account->account_id,
            	'other_payment' => 0
            ])
            ->where('current_balance', '>', 0)
            ->orderBy('due_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
	}

	public function penaltyForNonPayment()
	{
		$lastPayment = $this->prevBalance();
		if (!$lastPayment) {
			return;
		}

		$percent = dbConfig('penalty-non-payment');
		
		if($lastPayment->current_balance > 0
			&& $lastPayment->dueDate->lt($this->dueDate)) {
			return $lastPayment->current_balance * ($percent / 100); 
		}

		return 0;
	}

	public function adjustments()
	{
		return AdjustmentService::ins()
			->get([
				'account_id' => $this->account->account_id,
				'due_date' => $this->dueDate
			]);
	}
}