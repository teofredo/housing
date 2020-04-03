<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Transformers\AccountTransformer;
use App\Validators\AccountValidator;
use App\Services\AccountService;
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
    
    public function post(
    	Request $request,
    	AccountValidator $validator,
    	AccountService $accountService)
    {
    	try {
    		$data = $request->all();
    	
    		$validator->validate($data);
    		
    		DB::beginTransaction();
    		
    		$account = $accountService->createAccount();
    		
    		DB::commit();
    		
    		$resource = $this->fractal->item($account, $this->transformer)->get();
    		
    		return response($resource);
    		
    	} catch(\Exception $e) {}
    	
    	DB::rollBack();
    	
    	$errorResponse = new ErrorResponse($e);
    	
    	return $errorResponse->toJson();
    }
}
