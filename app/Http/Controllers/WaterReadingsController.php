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
use Illuminate\Support\Facades\DB;

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
    	WaterReadingValidator $validator)
    {
    	try {
    		$data = $request->all();
    		
    		$validator->validate($data);
    		
    		DB::beginTransaction();
    		
    		$readingService->addWaterReading($data);
    		
    		DB::commit();
    		
    	} catch(\Exception $e) {}
    	
    	DB::rollBack();
    	
    	$errorResponse = new ErrorResponse($e);
    	
    	return $errorResponse->toJson();
    }
}
