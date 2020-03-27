<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\{
    FractalService,
    ErrorResponse
};

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $fractal;

    public function __construct(
    	FractalService $fractal)
    {
    	$this->fractal = $fractal;

        $this->initForResource();
    }

    private function initForResource()
    {
        try {
            if(!$this->model || !$this->transformer) {
                return;
            }

            //model
        	$model = $this->model;
        	$this->model = new $model;
        	if(!$this->model instanceof Model) {
        		throw new \Exception("Class {$model} must return an instance of Illuminate\Database\Eloquent\Model;");
        	}

        	//transformer
        	$transformer = $this->transformer;
        	$this->transformer = new $transformer;

        } catch(\Exception $e) {
            // throw $e;
        }
    }

    public function index($id=null, Request $request)
    {
        try {
            if(!$this->model || !$this->transformer) {
                throw new \Exception('controller requires model and transformer definition');
            }

        	$includes = $request->get('_includes');

    		if(!$id) {
    			$resource = $this->model->all();
    			return $this->fractal
    				->collection($resource, $this->transformer)
    				->includes($includes)
    				->get();
    		}

    		$resource = $this->model->find($id);
    		return $this->fractal
    			->item($resource, new $this->transformer)
    			->includes($includes)
    			->get();

        } catch(\Exception $e) {}

        throw $e;
    }
}