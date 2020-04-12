<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonthlyDue;
use App\Transformers\MonthlyDueTransformer;
use App\Validators\MonthlyDueValidator;
use App\Services\{
	MonthlyDueService,
	ErrorResponse,
	ProcessService
};
use Illuminate\Support\Facades\{
	DB,
	Artisan
};
use Carbon\Carbon;

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
    * TODO: use queue workers
    */
    public function postOverride(Request $request)
    {
    	try {
    		$request->validate([
    			'due_date' => 'required|date_format:Y-m-d'
    		]);

    		/**
    		* using artisan console to generate month dues for all accounts
    		* to handle big process
    		*/
    		$exitCode = Artisan::call('generate:month-dues', [
    			'due_date' => $request->due_date
    		]);

    		$process = ProcessService::ins()->first([
    			'name' => 'generate-month-dues',
    			'due_date' => Carbon::parse($request->due_date)
    		]);

    		return response()->json($process);

    	} catch(\Exception $e) {}

    	$errorResponse = new ErrorResponse($e);

    	return $errorResponse->toJson();
    }
}
