<?php
namespace App\Transformers;

use App\Models\Block;

class BlockTransformer extends AbstractTransformer
{
	protected $model = Block::class;
	
	protected $availableIncludes = ['lots', 'householders'];
	
	public function includeLots(Block $model)
	{
		$lots = $model->lots;
		return $this->collection($lots, new LotTransformer);
	}
	
	public function includeHouseholders(Block $model)
	{
		$householders = $model->householders;
		return $this->collection($householders, new HouseholderTransformer);
	}
}