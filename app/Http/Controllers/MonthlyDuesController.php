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

    public function postOverride(Request $request)
    {
    	$command = 'generate:month-dues';
    	$processName = 'generate-month-dues';

    	try {
    		$request->validate([
    			'due_date' => 'required|date_format:m/Y'
    		]);

    		$dueDate = $request->due_date;

    		/**
    		* check if already processing or done 
    		*/
    		$process = ProcessService::ins()->first([
                'name' => $processName,
                'due_date' => $dueDate
            ]);

            if($process && in_array($process->status, ['processing', 'done'])) {
                return response()->json(['status' => $process->status]);
            }

            /**
            * create/ update process with status=pending
            */
    		$process = ProcessService::ins()
                ->getModel()
                ->updateOrCreate(
                    ['name' => $processName, 'due_date' => $dueDate],
                    ['status' => 'pending']
                );

            /**
            * queue generator command
            */
    		Artisan::queue($command, [ 'process_id' => $process->process_id ])
				->onConnection(env('QUEUE_CONNECTION'))
				->onQueue('commands');

    		return response()->json($process);

    	} catch(\Exception $e) {}

    	$errorResponse = new ErrorResponse($e, $request);

    	return $errorResponse->toJson();
    }

    public function _getSummary($id, Request $request)
    {
        $dueDate = $request->get('due_date', null);

        $result = MonthlyDueService::ins()->getSummary($dueDate);

        return response()->json(['data' => $result]);
    }
}