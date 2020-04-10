<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterRate;
use App\Transformers\WaterRateTransformer;
use App\Validators\WaterRateValidator;

class WaterRatesController extends Controller
{
    protected $model = WaterRate::class;
    protected $transformer = WaterRateTransformer::class;
    protected $validator = WaterRateValidator::class;
    
    public function index($id=null, Request $request)
    {
    	return parent::index($id, $request);
    }
    
    public function postOverride(
    	Request $request,
    	WaterRateValidator $validator
    ) {
    	return $this->setVConstraints(['min_m3' => $request->min_m3 ?? null])->post($request);
    }
}
