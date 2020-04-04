<?php
namespace App\Transformers;

class AccountTransformer extends AbstractTransformer
{
	protected $model = \App\Models\Account::class;
	
	public function transform($model)
	{
		if(!$model instanceof $this->model) {
			return [];
		}
		
		return [
			'account_id' => $model->account_id,
			'account_no' => $model->account_no,
			'parent_id' => $model->parent_id,
			'lastname' => $model->lastname,
			'firstname' => $model->firstname,
			'middlename' => $model->middlename,
			'suffix' => $model->suffix,
			'email' => $model->email,
			'username' => $model->username,
			'activated_at' => $model->activated_at,
			'active' => $model->active,
			'created_at' => $model->created_at
		];
	}
}