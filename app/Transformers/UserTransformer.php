<?php
namespace App\Transformers;

class UserTransformer extends AbstractTransformer
{
	protected $model = \App\Models\User::class;
	
	public function transform($model)
	{
		if(!$model instanceof $this->model) {
			return [];
		}
		
		return [
			'user_id' => (integer) $model->id,
			'name' => $model->name,
			'username' => $model->username,
			'email' => $model->email,
			'user_type' => $model->user_type,
			'email_verified_at' => $model->email_verified_at,
			'created_at' => $model->created_at
		];
	}
}