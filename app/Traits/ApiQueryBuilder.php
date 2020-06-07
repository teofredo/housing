<?php
namespace App\Traits;

use Illuminate\Support\{
	Str,
	Arr
};
use Illuminate\Http\Request;

trait ApiQueryBuilder
{
	public function parseWhere($wheres)
	{
		$where = [];

		$wheres = explode(',', $wheres);
		foreach($wheres as $w) {
			if(!Str::contains($w, ':')) {
				continue;
			}

			list($key, $value) = explode(':', $w);
			$where[$key] = $value;
		}

		return $where;
	}

	public function parseOrderBy($orderbys)
	{
		$orderBy = [];

		$orderbys = explode(',', $orderbys);
		foreach($orderbys as $o) {
			if(!Str::contains($o, ':')) {
				continue;
			}

			list($key, $value) = explode(':', $o);
			$orderBy[$key] = $value;
		}

		return $orderBy;
	}

	public function buildQuery(Request $request)
	{
		$model = $this->getModel();
		$data = $request->all();

		//check if there are query builder keys in data
		$queryBuilderKeys = config('api.query_builder_keys');
		$qbKeysInData = Arr::where($data, function($value, $key) use($queryBuilderKeys) {
			return in_array($key, $queryBuilderKeys);
		});

		if(!$qbKeysInData) {
			return $model->all();
		}

		$readfn = 'get';

		if(isset($data['_where'])) {
			$where = $this->parseWhere($data['_where']);
            $model = $model->where($where);
            $readfn = 'get';
		}

		if ($gt = Arr::get($data, '_gt')) {
			$where = $this->parseWhere($gt);
			$this->buildWhere($model, $where, '>');
			$readfn = 'get';
		} 

		if ($gte = Arr::get($data, '_gte')) {
			$where = $this->parseWhere($gte);
			$this->buildWhere($model, $where, '>=');
			$readfn = 'get';
		} 

		if ($lt = Arr::get($data, '_lt')) {
			$where = $this->parseWhere($lt);
			$this->buildWhere($model, $where, '<');
			$readfn = 'get';
		} 

		if ($lte = Arr::get($data, '_lte')) {
			$where = $this->parseWhere($lte);
			$this->buildWhere($model, $where, '<=');
			$readfn = 'get';
		}

		if(isset($data['_find'])) {
			$where = $this->parseWhere($data['_find']);
			$model = $model->where($where);
			$readfn = 'first';
		}

		if(isset($data['_latest'])) {
			$where = $this->parseWhere($data['_latest']);
			$model = $model->where($where)->latest();
			$readfn = 'first';
		}

		if(isset($data['_orderby'])) {
        	$orderBy = $this->parseOrderBy($data['_orderby']);
        	foreach($orderBy as $key => $value) {
        		$model = $model->orderBy($key, $value);
        	}
        }

		if(isset($data['_limit'])) {
			$model = $model->limit($data['_limit']);
		}

		return $model->$readfn();
	}

	private function buildWhere(&$model, $where, $condition)
	{
		foreach($where as $key => $value) {
			$model = $model->where($key, $condition, $value);
		}
	}
}