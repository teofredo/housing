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
    
    public function _putCancelPlan(
        $id=null, 
        Request $request,
        InternetSubscriptionService $subscriptionService
    ) {
        try {
            $data = $request->all();
            
            if (!isset($data['cancel_plan'])
                || !isset($data['cancel_reason'])) {
                throw new ValidationException('cancel_plan and cancel_reason is required.');
            }
            
            DB::beginTransaction();
            
            $subscriptionService->cancelPlan($id, $data);
            
            DB::commit();
            
            return response()->json(['success' => true]);
            
        } catch(\Exception $e) {}
        
        DB::rollBack();
        
        $errorResponse = new ErrorResponse($e, $request);
        
        return $errorResponse->toJson();
    }
}