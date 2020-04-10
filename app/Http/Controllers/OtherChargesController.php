<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtherCharge;
use App\Transformers\OtherChargeTransformer;
use App\Validators\OtherChargeValidator;
use App\Services\{
	OtherChargeService,
	ErrorResponse
};
use Illuminate\Support\Arr;

class OtherChargesController extends Controller
{
    protected $model = OtherCharge::class;
    protected $transformer = OtherChargeTransformer::class;
    protected $validator = OtherChargeValidator::class;

    public function index($id=null, Request $request)
    {
    	return parent::index($id, $request);
    }

    public function postOverride(
    	Request $request,
    	OtherChargeValidator $validator,
    	OtherChargeService $service
    ) {
    	try { 
    		$data = $request->all();

    		$validator->validate($data);

    		$otherCharge = OtherChargeService::ins()->first(Arr::only($data, ['account_id', 'fee_id', 'due_date']));
    		if($otherCharge && $otherCharge->fee->code != 'other-fee') {
    			throw new \Exception("{$otherCharge->fee->name} already added");
    		}

    		$resource = OtherChargeService::ins()->add($data);
    		$resource = $this->fractal->item($resource, $this->transformer)->get();

    		return $resource;

    	} catch(\Exception $e) {}

    	$errorResponse = new ErrorResponse($e);

    	return $errorResponse->toJson();
    }
}
