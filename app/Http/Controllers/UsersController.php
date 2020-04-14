<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
	Request,
	Response
};
use App\Models\User;
use App\Transformers\UserTransformer;
use App\Validators\UserValidator;

class UsersController extends Controller
{
	protected $model = User::class;
	protected $transformer = UserTransformer::class;
	protected $validator = UserValidator::class;
	
	public function index($id=null, Request $request)
	{
		return parent::index($id, $request);
	}
	
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
            $cookies = $request->cookies->all();
            
            if(isset($cookies['user_id']) 
                && isset($cookies['access_token']) 
                && isset($cookies['cc_token'])) {
                
                $user = $this->authApiService->getUserByAccessToken($cookies['access_token']);
                $user = User::find($user->id);
                
                $user = $this->fractal->item($user, new UserTransformer)->get();
                
                return response($user);
            }
            
            $data = $request->all();
            
            $token = $this->requestToken('password', $data);
            $user = $this->getAuthUser();
            
            $response = response()->json($token);
            
            $expire = time() + 86400;
            $domain = '.' . env('CLIENT_DOMAIN');
            $ccToken = $this->requestToken('client_credentials')->access_token;
            
            $response->cookie('user_id', $user->id, $expire, '/', $domain)
                ->cookie('access_token', $token->access_token, $expire, '/', $domain)
                ->cookie('cc_token', $ccToken, $expire, '/', $domain);
                
            return $response;
        } 
        catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();
    }
}
