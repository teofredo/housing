<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtherCharge;
use App\Transformers\OtherChargeTransformer;
use App\Validators\OtherChargeValidator;

class OtherChargesController extends Controller
{
    protected $model = OtherCharge::class;
    protected $transformer = OtherChargeTransformer::class;
    protected $validator = OtherChargeValidator::class;

    public function index($id=null, Request $request)
    {
    	return parent::index($id, $request);
    }
}
