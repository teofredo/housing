<?php
namespace App\Transformers;

use App\Models\Config;

class ConfigTransformer extends AbstractTransformer
{
	protected $model = Config::class;
}