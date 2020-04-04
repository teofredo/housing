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

class AccountsController extends Controller
{
    protected $model = Account::class;
    protected $transformer = AccountTransformer::class;
    protected $validator = AccountValidator::class;
    
    public function index($id=null, Request $request)
    {
    	return parent::index($id, $request);
    }
    
    public function postOverride(
    	Request $request,
    	AccountValidator $validator,
    	AccountService $accountService)
    {
    	try {
    		$data = $request->all();
    	
    		$validator->validate($data);
    		
    		DB::beginTransaction();
    		
    		$resource = $accountService->createAccount($data);
    		$resource = $this->fractal->item($resource, $this->transformer)->get();
            
            DB::commit();
    		
    		return response($resource);
    		
    	} catch(\Exception $e) {}
    	
    	DB::rollBack();
    	
    	$errorResponse = new ErrorResponse($e);
    	
    	return $errorResponse->toJson();
    }
}