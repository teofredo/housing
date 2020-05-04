<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternetSubscription;
use App\Transformers\InternetSubscriptionTransformer;
use App\Validators\InternetSubscriptionValidator;
use App\Services\{
	InternetSubscriptionService,
	ErrorResponse
};
use Carbon\Carbon;

class InternetSubscriptionsController extends Controller
{
    protected $model = InternetSubscription::class;
    protected $transformer = InternetSubscriptionTransformer::class;
    protected $validator = InternetSubscriptionValidator::class;
    
    public function postOverride(
    	Request $request,
    	InternetSubscriptionService $subscriptionService,
    	InternetSubscriptionValidator $validator
    ) {
    	try {
    		$data = $request->all();
    		
    		$validator->validate($data);
    		
    		if($result = $subscriptionService->latest([
    			'account_id' => $data['account_id'],
    			'active' => 1
    		])) {
    			throw new \Exception('account has already subscribed to a plan');
    		}
    		
    		$resource = $subscriptionService->add($data);
    		$resource = $this->fractal->item($resource, $this->transformer)->get();
    		
    		return response($resource);
    		
    	} catch(\Exception $e) {}
    	
    	$errorResponse = new ErrorResponse($e);
    	
    	return $errorResponse->toJson();
    }
}
