<?php
namespace App\Validators;

class LotValidator extends BaseValidator
{
	protected $rules = [
		'block_id' => 'required|integer',
		'name' => 'required|string'
	];
	
	protected $messages = [
		'block_id.required' => 'block_id is required',
		'name.required' => 'lot name is required'
	];
	
	protected function overrideRules()
	{
		$this->rules['name'] = "required|string|unique:lots,name,NULL,id,block_id,{$this->constraints['block_id']},deleted_at,NULL";
		$this->messages['name.unique'] = 'lot name has already been taken';
		
		
	}
}