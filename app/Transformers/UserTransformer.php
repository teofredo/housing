<?php
namespace App\Transformers;

class UserTransformer extends AbstractTransformer
{
	protected $model = \App\Models\User::class;
	
	public function transform($model)
	{
		if(!$model instanceof \Illuminate\Database\Eloquent\Model) {
			return [];
		}
		
		return [
			'user_id' => (integer) $model->id,
			'name' => $model->name,
			'email_verified_at' => $model->email_verified_at,
			'created_at' => $model->created_at
		];
	}
}