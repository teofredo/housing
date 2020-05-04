<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Householder;
use App\Transformers\HouseholderTransformer;
use App\Validators\HouseholderValidator;

class HouseholdersController extends Controller
{
    protected $model = Householder::class;
    protected $transformer = HouseholderTransformer::class;
    protected $validator = HouseholderValidator::class;
}
