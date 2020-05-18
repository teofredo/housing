<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Transformers\PaymentTransformer;
use App\Validators\PaymentValidator;
use App\Services\{
	ErrorResponse,
	PaymentService
};
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    protected $model = Payment::class;
    protected $transformer = PaymentTransformer::class;
    protected $validator = PaymentValidator::class;

    public function postOverride(
    	Request $request,
    	PaymentService $paymentService,
    	PaymentValidator $validator
    ) {
    	try {
    		$data = $request->all();

    		$validator->validate($data);

    		DB::beginTransaction();

    		$resource = $paymentService->addPayment($data);
    		$resource = $this->fractal->item($resource, $this->transformer)->get();

    		DB::commit();

    		return response($resource);

    	} catch(\Exception $e) {}

    	DB::rollBack();

    	$errorResponse = new ErrorResponse($e, $request);

    	return $errorResponse->toJson();
    }
}
