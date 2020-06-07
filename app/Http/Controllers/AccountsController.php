<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Transformers\AccountTransformer;
use App\Validators\AccountValidator;
use App\Services\{
    AccountService,
    ErrorResponse
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class AccountsController extends Controller
{
    protected $model = Account::class;
    protected $transformer = AccountTransformer::class;
    protected $validator = AccountValidator::class;
    
    public function postOverride(
    	Request $request,
    	AccountValidator $validator,
    	AccountService $accountService
    ) {
    	try {
    		$data = $request->all();
    		$validator->validate($data);
    		
    		DB::beginTransaction();
    		
    		$resource = $accountService->createAccount($data);
            $resource = $this->fractal
                ->item($resource, $this->transformer)
                ->includes('householder')
                ->get();
            
            DB::commit();
    		
    		return response($resource);
    		
    	} catch(\Exception $e) {}
    	
    	DB::rollBack();
    	
    	$errorResponse = new ErrorResponse($e, $request);
    	
    	return $errorResponse->toJson();
    }

    public function _getAccountSummary($id, Request $request)
    {
        try {
            // $summary = AccountService::ins()->getAccountSummary($id);
        } catch(\Exception $e) {}

        DB::rollBack();

        $errorResponse = new ErrorResponse($e, $request);

        return $errorResponse->toJson();
    }
}