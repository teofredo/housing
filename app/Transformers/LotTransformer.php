<?php
namespace App\Transformers;

use App\Models\Lot;

class LotTransformer extends AbstractTransformer
{
	protected $model = Lot::class;
	
	protected $availableIncludes = ['block'];
	
	public function includeBlock(Lot $model)
	{
		$block = $model->block;
		return $this->item($block, new BlockTransformer);
	}
}