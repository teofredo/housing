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
use Illuminate\Support\Facades\DB;
use App\Exceptions\ValidationException;

class InternetSubscriptionsController extends Controller
{
    protected $model = InternetSubscription::class;
    protected $transformer = InternetSubscriptionTransformer::class;
    protected $validator = InternetSubscriptionValidator::class;
    
    public function deleteOverride($id, Request $request)
    {
        try {
            DB::beginTransaction();
            
            $subscription = InternetSubscription::findOrFail($id);
            $subscription->active = null;
            $subscription->save();
            $subscription->delete();
            
            DB::commit();
            
            return response()->json(['success' => true]);
            
        } catch(\Exception $e) {}
        
        DB::rollBack();
        
        $errorResponse = new ErrorResponse($e, $request);
        
        return $errorResponse->toJson();
    }
    
    public function _getTerminationSummary($id=null, Request $request)
    {
        $subscriptionService = new InternetSubscriptionService;
        
        try {
            $result = $subscriptionService->getTerminationSummary($id);
            
            return response()->json(['data' => $result->summary]);
            
        } catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e, $request);
        
        return $errorResponse->toJson();
    }
    
    public function _putCancelPlan($id=null, Request $request)
    {
        $subscriptionService = new InternetSubscriptionService;
        
        try {
            $data = $request->all();
            
            if (!isset($data['cancel_plan'])
                || !isset($data['cancel_reason'])) {
                throw new ValidationException('cancel_plan and cancel_reason is required.');
            }
            
            DB::beginTransaction();
            
            $subscription = $subscriptionService->cancelPlan($id, $data);
            
            DB::commit();
            
            return $this->fractal->item($subscription, $this->transformer)->get();
            
        } catch(\Exception $e) {}
        
        DB::rollBack();
        
        $errorResponse = new ErrorResponse($e, $request);
        
        return $errorResponse->toJson();
    }
    
    public function _putChangePlan($id=null, Request $request)
    {
        try {
            $data = $request->all();
            
            if (!isset($data['plan_id'])) {
                throw new ValidationException('plan_id is required.');
            }
            
            DB::beginTransaction();
            
            $response = InternetSubscriptionService::ins()->changePlan($id, $data);
            
            DB::commit();
            
            return response()->json($response);
            
        } catch(\Exception $e) {}
        
        DB::rollBack();
        
        $errorResponse = new ErrorResponse($e, $request);
        
        return $errorResponse->toJson();
    }
}