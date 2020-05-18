<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adjustment;
use App\Transformers\AdjustmentTransformer;
use App\Validators\AdjustmentValidator;

class AdjustmentsController extends Controller
{
    protected $model = Adjustment::class;
    protected $transformer = AdjustmentTransformer::class;
    protected $validator = AdjustmentValidator::class;
}
