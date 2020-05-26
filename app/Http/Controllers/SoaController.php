<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Soa;
use App\Validators\SoaValidator;
use App\Transformers\SoaTransformer;

class SoaController extends Controller
{
    protected $model = Soa::class;
    protected $transformer = SoaTransformer::class;
    protected $validator = SoaValidator::class;
}
