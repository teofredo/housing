<?php
namespace App\Services;

use App\Models\Config;

class ConfigService extends AbstractService
{
	protected static $class = __CLASS__;
	
	public function model()
	{
		return Config::class;
	}

	public function add(array $data=[])
	{
		$arr = [];
    	$arr['value'] = $data['value'] ?? null;

    	if(isset($data['comment'])) {
    		$arr['comment'] = $data['comment'] ?? null;
    	}

    	return Config::updateOrCreate(['key' => $data['key']], $arr);
	}
}