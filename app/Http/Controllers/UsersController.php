<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
	Request,
	Response
};
use App\Models\User;
use App\Transformers\UserTransformer;
use App\Validators\UserValidator;
use App\Services\ErrorResponse;

class UsersController extends Controller
{
	protected $model = User::class;
	protected $transformer = UserTransformer::class;
	protected $validator = UserValidator::class;
	
	/**
	* create/register admin users
	*/
    public function postOverride(
        Request $request, 
        UserValidator $validator
    ) {
        try {
        	$data = $request->all();
        	
            $validator->validate($data);
            
            $data['password'] = bcrypt($data['password']);

            $user = User::create($data);
            if(!$user) {
            	throw new \Exception('failed to create new user');
            }
            
            $resource = $this->fractal->item($user, $this->transformer)->get();
            
            return response($resource);
            
        } catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();
    }
    
    public function login(Request $request) 
    {
        try {
            $data = $request->all();
            
            $token = $this->requestToken('password', $data);
            $user = $this->getAuthUser();
            
            return response()->json($token);
        } 
        catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();
    }
}
