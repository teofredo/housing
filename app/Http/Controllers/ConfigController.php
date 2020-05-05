<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use App\Transformers\ConfigTransformer;
use App\Validators\ConfigValidator;
use App\Services\{
    ConfigService,
    ErrorResponse
};

class ConfigController extends Controller
{
    protected $model = Config::class;
    protected $transformer = ConfigTransformer::class;
    protected $validator = ConfigValidator::class;

    public function postOverride(
    	Request $request,
    	ConfigValidator $validator
    ) {
    	try {
    		$data = $request->all();
    		$validator->validate($data);

	    	$resource = ConfigService::ins()->add($data);
    		$resource = $this->fractal->item($resource, $this->transformer)->get();

    		return response($resource);

    	} catch(\Exception $e) {}

    	$errorResponse = new ErrorResponse($e);

    	return $errorResponse->toJson();
    }
}
