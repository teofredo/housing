<?php
namespace App\Services;

use App\Models\Account;
use Carbon\Carbon;

class AccountService extends AbstractService
{
	public function model()
	{
		return Account::class;
	}
	
	public function createAccount(array $data=[])
	{
		//encrypt password
		$data['password'] = bcrypt($data['password']);
		
		$account = $this->model->create($data);
		
		if(!$account) {
			throw new \Exception('failed to create account');
		}
		
		//create formatted account_no
		$year = Carbon::now()->format('Y');
		$parentId = sprintf('%04s', $account->parent_id);
		$accountId = sprintf('%04s', $account->account_id);
		
		$account->account_no = "{$year}{$parentId}{$accountId}";
		$account->save();
		
		return $account;
	}
}