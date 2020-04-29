<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait ApiQueryBuilder
{
	public function parseWhere($wheres)
	{
		$where = [];

		$wheres = explode(',', $wheres);
		foreach($wheres as $w) {
			if(!Str::contains($w, '=')) {
				continue;
			}

			list($key, $value) = explode('=', $w);
			$where[$key] = $value;
		}

		return $where;
	}
}