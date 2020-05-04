<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterReading;
use App\Transformers\WaterReadingTransformer;
use App\Validators\WaterReadingValidator;
use App\Services\{
	WaterReadingService,
	ErrorResponse
};

class WaterReadingsController extends Controller
{
    protected $model = WaterReading::class;
    protected $transformer = WaterReadingTransformer::class;
    protected $validator = WaterReadingValidator::class;
    
    public function index($id=null, Request $request)
    {
    	return parent::index($id, $request);
    }
    
    public function postOverride(
    	Request $request,
    	WaterReadingService $readingService,
    	WaterReadingValidator $validator
    ) {
    	try {
    		$data = $request->all();

    		$validator
                ->setConstraints(['account_id' => $request->account_id ?? null])
                ->validate($data);
    		
    		$resource = $readingService->addWaterReading($data);
            $resource = $this->fractal->item($resource, $this->transformer)->get();

            return response($resource);
    		
    	} catch(\Exception $e) {}
    	
    	$errorResponse = new ErrorResponse($e);
    	
    	return $errorResponse->toJson();
    }
}
