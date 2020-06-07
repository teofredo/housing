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
use Illuminate\Support\Arr;

class WaterReadingsController extends Controller
{
    protected $model = WaterReading::class;
    protected $transformer = WaterReadingTransformer::class;
    protected $validator = WaterReadingValidator::class;
    
    public function postOverride(
    	Request $request,
    	WaterReadingService $readingService,
    	WaterReadingValidator $validator
    ) {
    	try {
    		$data = $request->all();
    		$validator->validate($data);
    		
    		$resource = $readingService->saveWaterReading($data);
            $resource = $this->fractal->item($resource, $this->transformer)->get();

            return response($resource);
    		
    	} catch(\Exception $e) {}
    	
    	$errorResponse = new ErrorResponse($e, $request);
    	
    	return $errorResponse->toJson();
    }
    
    public function putOverride(
        $id=null, 
        Request $request,
        WaterReadingService $readingService,
        WaterReadingValidator $validator
    ) {
        try {
            $data = $request->all();
            $data['update_id'] = $id;
            
            $validator->validate($data);
            
            $resource = $readingService->saveWaterReading(Arr::except($data, 'update_id'), $id);
            $resource = $this->fractal->item($resource, $this->transformer)->get();

            return response($resource);
            
        } catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e, $request);
        
        return $errorResponse->toJson();   
    }
}
