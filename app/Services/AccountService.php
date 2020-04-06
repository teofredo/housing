<?php
namespace App\Services;

use App\Models\{
	Account,
	Householder
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
		$data['password'] = bcrypt($data['password']);
		
		//build account_name
		$middlename = $data['middlename'] ?? null;
		$mi = $middlename[0];
		$suffix = $data['suffix'] ?? null;
		$data['account_name'] = strtoupper("{$data['firstname']} {$mi}. {$data['lastname']} {$suffix}");
		
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
		
		//build house_no and water_meter_no
		
		
		if(!$householder) {
			throw new \Exception('failed to add householder info');
		}
		
		return $account;
	}
}