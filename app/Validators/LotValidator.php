<?php
namespace App\Validators;

class LotValidator extends BaseValidator
{
	private $rules = [
		'block_id' => 'required|integer',
		'name' => 'required|string'
	];
	
	protected $messages = [
		'block_id.required' => 'block_id is required',
		'name.required' => 'lot name is required',
		'name.unique' => 'lot name has already been taken'
	];
	
	// protected function overrideRules()
	// {
	// 	$this->rules['name'] = "required|string|unique:lots,name,NULL,id,block_id,{$this->constraints['block_id']},deleted_at,NULL";
	// }
	
	public function getRules()
	{
		// update rules
		if (isset($this->data['update_id'])) {
			return array_merge($this->rules, [
				'name' => [
					'required',
					'string',
					"unique:lots,name,{$this->data['update_id']},lot_id,block_id,{$this->data['block_id']},deleted_at,NULL"
				]
			]);
		}
		
		return array_merge($this->rules, [
			'name' => [
				'required',
				'string',
				"unique:lots,name,NULL,id,block_id,{$this->data['block_id']},deleted_at,NULL"
			]
		]);
	}
}