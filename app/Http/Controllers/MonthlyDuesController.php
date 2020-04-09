<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonthlyDue;
use App\Transformers\MonthlyDueTransformer;
use App\Validators\MonthlyDueValidator;
use App\Services\{
	MonthlyDueService,
	ErrorResponse
};
use Illuminate\Support\Facades\DB;

class MonthlyDuesController extends Controller
{
    protected $model = MonthlyDue::class;
    protected $transformer = MonthlyDueTransformer::class;
    protected $validator = MonthlyDueValidator::class;

    public function index($id=null, Request $request)
    {
    	return parent::index($id, $request);
    }

    /**
    * monthly dues generator
    */
    public function postOverride(
    	Request $request,
    	MonthlyDueValidator $validator,
    	MonthlyDueService $monthDueService)
    {
    	try {
    		$data = $request->all();

    		$validator->validate($data);

    		DB::beginTransaction();

    		$monthDueService->generateMonthDue($data['due_date'] ?? null);

    		DB::commit();

    		return;

    	} catch(\Exception $e) {}

    	DB::rollBack();

    	$errorResponse = new ErrorResponse($e);

    	return $errorResponse->toJson();
    }
}
