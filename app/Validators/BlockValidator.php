<?php
namespace App\Validators;

class BlockValidator extends BaseValidator
{
	private $rules = [
		'name' => 'required|string|unique:blocks,name,NULL,id,deleted_at,NULL'
	];
	
	protected $messages = [
		'name.required' => 'block name is required',
		'name.unique' => 'block name already exists'
	];
	
	public function getRules()
	{
		// update rules
		if (isset($this->data['update_id'])) { 
			return [
				'name' => [
					'required',
					'string',
					"unique:blocks,name,{$this->data['update_id']},block_id,deleted_at,NULL"
				]
			];
		}
		
		return $this->rules;
	}
}