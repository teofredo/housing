<?php
namespace App\Transformers;

use App\Models\Householder;

class HouseholderTransformer extends AbstractTransformer
{
	protected $model = Householder::class;
	
	protected $availableIncludes = ['lot', 'account'];
	
	public function transform($model)
	{
		if(!$model instanceof Householder) {
			return [];
		}
		
		$name = json_decode($model->name);
		
		return [
			'householder_id' => (int) $model->householder_id,
			'account_id' => (int) $model->account_id,
			'house_no' => $model->house_no,
			'water_meter_no' => $model->water_meter_no,
			'type' => $model->type,
			'block_id' => (int) $model->block_id,
			'lot_id' => (int) $model->lot_id,
			'firstname' => $name->first,
			'lastname' => $name->last,
			'middlename' => $name->middle,
			'suffix' => $name->suffix,
			'contact_no' => $model->contact_no,
			'moved_in' => $model->moved_in,
			'remarks' => $model->remarks,
			'created_at' => $model->created_at
		];
	}
	
	public function includeLot(Householder $model)
	{
		$lot = $model->lot;
		return $this->item($lot, new LotTransformer);
	}

	public function includeAccount(Householder $model)
	{
		$account = $model->account;
		return $this->item($account, new AccountTransformer);
	}
}