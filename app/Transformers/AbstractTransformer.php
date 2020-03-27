<?php
namespace App\Transformers;

use League\Fractal;

abstract class AbstractTransformer extends Fractal\TransformerAbstract
{
	public function transform($model)
	{
		if(!$model instanceof $this->model) {
			return [];
		}

		$response = [];
		$excludes = ['updated_at', 'deleted_at'];

		$fields = $model->getTableColumns();

		foreach($fields as $field){
			if(in_array($field, $excludes)) {
				continue;
			}

			$response[$column] = $model->$column;
		}

		return $response;
	}
}