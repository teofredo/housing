<?php
namespace App\Transformers;

use App\Models\Soa;

class SoaTransformer extends AbstractTransformer
{
	protected $model = Soa::class;
	
	protected $availableIncludes = [
		// 
	];
}