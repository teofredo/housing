<?php
namespace App\Validators;

class FeeValidator extends BaseValidator
{
	private $rules = [
		'name' => 'required|string|unique:fees,name,NULL,block_id,deleted_at,NULL',
		'fee' => 'required|numeric|min:0',
		'other_fee' => 'sometimes|boolean',
		'deleble' => 'sometimes|boolean'
	];
	
	protected $messages = [
		'name.required' => 'fee name is required',
		'name.unique' => 'fee name already added',
		'fee.required' => 'fee amount is required'
	];
	
	public function getRules()
	{
		if (isset($this->data['update_id'])) {
			$this->rules['name'] = "required|string|unique:fees,name,{$this->data['update_id']},fee_id,deleted_at,NULL";
		}
		
		return $this->rules;
	}
}