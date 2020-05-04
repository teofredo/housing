<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use App\Transformers\ConfigTransformer;
use App\Validators\ConfigValidator;

class ConfigController extends Controller
{
    protected $model = Config::class;
    protected $transformer = ConfigTransformer::class;
    protected $validator = ConfigValidator::class;
}
