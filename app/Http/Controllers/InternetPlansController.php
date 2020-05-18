<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternetPlan;
use App\Transformers\InternetPlanTransformer;
use App\Validators\InternetPlanValidator;

class InternetPlansController extends Controller
{
    protected $model = InternetPlan::class;
    protected $transformer = InternetPlanTransformer::class;
    protected $validator = InternetPlanValidator::class;
}
