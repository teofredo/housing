<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Process;
use App\Transformers\ProcessTransformer;
use App\Validators\ProcessValidator;

class ProcessController extends Controller
{
    protected $model = Process::class;
    protected $transformer = ProcessTransformer::class;
    protected $validator = ProcessValidator::class;
}
