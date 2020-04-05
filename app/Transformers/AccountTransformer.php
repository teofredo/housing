<?php
namespace App\Transformers;

use App\Models\Account;

class AccountTransformer extends AbstractTransformer
{
	protected $model = Account::class;
	
	protected $availableIncludes = ['householder'];
	
	public function transform($model)
	{
		if(!$model instanceof Account) {
			return [];
		}
		
		return [
			'account_id' => (int) $model->account_id,
			'account_no' => $model->account_no,
			'parent_id' => $model->parent_id,
			'account_name' => $model->account_name,
			'email' => $model->email,
			'username' => $model->username,
			'activated_at' => $model->activated_at,
			'active' => $model->active,
			'created_at' => $model->created_at
		];
	}
	
	public function includeHouseholder(Account $model)
	{
		$householder = $model->householder;
		return $this->item($householder, new HouseholderTransformer);
	}
}