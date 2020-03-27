<?php
namespace App\Services;

use League\Fractal;
use League\Fractal\Manager;

class FractalService extends Fractal\TransformerAbstract
{
	public function __construct()
	{
		$this->fractal = new Manager;
	}

	public function item($data, $transformer, $resourceKey='')
	{
		$this->resource = parent::item($data, $transformer, $resourceKey);
		return $this;
	}

	public function collection($data, $transformer, $resourceKey='')
	{
		$this->resource = parent::collection($data, $transformer, $resourceKey='');
		return $this;
	}

	public function includes($includes=null)
	{
		if($includes) {
			$this->fractal->parseIncludes($includes);
		}

		return $this;
	}

	public function get()
	{
		return $this->fractal->createData($this->resource)->toJson();
	}
}