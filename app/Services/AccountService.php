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
	public function model()
	{
		return Account::class;
	}
	
	public function createAccount(array $data=[])
	{
		$data['username'] = $data['email'];
		$data['password'] = bcrypt($data['password']);
		
		//build account_name
		$middleName = $data['middlename'] ?? null;
		$middleInitial = $middleName ? strtoupper($data['middlename'][0]) : null;
		$suffix = $data['suffix'] ?? null;
		$data['account_name'] = "{$data['firstname']} {$middleInitial}. {$data['lastname']} {$suffix}";
		
		//create account
		$account = $this->model->create($data);
		
		if(!$account) {
			throw new \Exception('failed to create account');
		}
		
		//build account_no and save
		$year = Carbon::now()->format('Y');
		$parentId = sprintf('%04s', $account->parent_id);
		$accountId = sprintf('%04s', $account->account_id);
		$account->account_no = "{$parentId}{$year}{$accountId}{$data['householderType']}";
		$account->save();
		
		//build householder data
		$data = Arr::only($data, ['block_id', 'lot_id', 'contact_no']);
		$data = array_merge([
			'account_id' => $account->account_id,
			'house_no' => $data['house_no'] ?? null,
			'type' => $data['householder_type'],
			'name' => json_encode([
				'first' => $data['firstname'],
				'last' => $data['lastname'],
				'middle' => $middleName,
				'suffix' => $suffix
			]),
			'moved_in' => $data['moved_in']
		]);
		
		//create householder
		Householder::create($data);
		
		return Account::with('householder')->find($account->account_id);
	}
}