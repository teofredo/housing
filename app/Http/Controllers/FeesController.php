<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fee;
use App\Transformers\FeeTransformer;
use App\Validators\FeeValidator;

class FeesController extends Controller
{
	protected $model = Fee::class;
	protected $transformer = FeeTransformer::class;
	protected $validator = FeeValidator::class;
	
    public function index($id=null, Request $request)
    {
    	return parent::index($id, $request);
    }
}
