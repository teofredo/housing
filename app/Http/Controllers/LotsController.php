<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lot;
use App\Transformers\LotTransformer;
use App\Validators\LotValidator;

class LotsController extends Controller
{
    protected $model = Lot::class;
    protected $transformer = LotTransformer::class;
    protected $validator = LotValidator::class;
    
    public function index($id=null, Request $request)
    {
    	return parent::index($id, $request);
    }
}
