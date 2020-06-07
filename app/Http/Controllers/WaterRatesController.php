<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterRate;
use App\Transformers\WaterRateTransformer;
use App\Validators\WaterRateValidator;
use App\Services\WaterReadingService;

class WaterRatesController extends Controller
{
    protected $model = WaterRate::class;
    protected $transformer = WaterRateTransformer::class;
    protected $validator = WaterRateValidator::class;
    
    public function putOverride($id=null, Request $request)
    {
    	$data = $request->all();
    	
    	if (isset($data['min_m3']) && $data['min_m3'] > 0) {
    		$data['min_fee'] = 0;
    	}
    	
    	return $this->put($id, $request, $data);
    }
}
