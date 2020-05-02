<?php
namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

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

	public function buildQuery(Request $request)
	{
		$model = $this->getModel();

		$data = $request->all();

		if(isset($data['_where'])) {
			$where = $this->parseWhere($data['_where']);
            return $model->where($where)->get();
		}

		if(isset($data['_find'])) {
			$where = $this->parseWhere($data['_find']);
			return $model->where($where)->first();
		}

		return $model->all();
	}
}