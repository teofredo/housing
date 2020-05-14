<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractService
{
	protected $model;

	private static $instance;

	public function __construct()
	{
		$this->initModel();
	}

	abstract public function model();

	public static function ins()
	{
		if (!self::$instance instanceof static::$class) {
			self::$instance = new static::$class;
		}

		return self::$instance;
	}

	private function initModel()
	{
		$model = $this->model();
		$model = new $model;

		if(!$model instanceof Model) {
			throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
		}

		$this->model = $model;
	}

	public function getModel()
	{
		return $this->model;
	}

	public function add(array $data=[])
	{
		return $this->model->create($data);
	}

	public function find($primaryKey)
	{
		return $this->model->find($primaryKey);
	}

	public function findOrFail($primaryKey)
	{
		return $this->model->findOrFail($primaryKey);
	}

	public function get(array $where=[], array $with=[])
	{
		return $this->model
			->where($where)
			->with($with)
			->get();
	}

	public function first(array $where=[], array $with=[])
	{
		return $this->model
			->where($where)
			->with($with)
			->first();
	}
	
	public function findBy($field, $value)
	{
		return $this->model->where($field, $value)->get();
	}
	
	public function findFirst($field, $value)
	{
		return $this->model->where($field, $value)->first();
	}
	
	public function latest(array $where=[], array $with=[])
	{
		return $this->model
			->where($where)
			->with($with)
			->latest()
			->first();
	}

	/*public function __get($fn)
	{
		$fn = 'get' . Str::studly($fn);
		if (method_exists($this, $fn) && is_callable([$this, $fn])) {
			return $this->$fn();
		}
	}*/
}