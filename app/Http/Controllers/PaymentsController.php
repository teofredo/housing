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
use Carbon\Carbon;

class PaymentsController extends Controller
{
    protected $model = Payment::class;
    protected $transformer = PaymentTransformer::class;
    protected $validator = PaymentValidator::class;

    public function postOverride(Request $request)
    {
    	$data = $request->all();

        if (!isset($data['current_balance'])
            && isset($data['amount_due'])
            && isset($data['amount_paid'])) {
            
            $data['current_balance'] = $data['amount_due'] - $data['amount_paid'];
        }

        return $this->post($request, $data);
    }

    public function putOverride($id=null, Request $request)
    {
        $data = $request->all();

        if (!isset($data['current_balance'])
            && isset($data['amount_due'])
            && isset($data['amount_paid'])) {
            
            $data['current_balance'] = $data['amount_due'] - $data['amount_paid'];
        }

        $data['paid_at'] = $data['paid_at'] ?? Carbon::now()->format('Y-m-d');

        return $this->put($id, $request, $data);
    }
}
