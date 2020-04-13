<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};

use App\Exceptions\ValidationException;
use App\Validators\UserValidator;
use Illuminate\Support\Carbon;
use App\Transformers\UserTransformer;

use App\Services\{
    ErrorResponse
};

use App\Models\{
    User
};

class AuthController extends Controller
{
    public function user(Request $request)
    {
        try {
            return $request->user();
        }
        catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();
    }

    public function logout(Request $request)
    {
        try {
            $request->user()
                ->token()
                ->revoke();

            return response()->json([
                'status' => 'success',
                'message' => 'logged out'
            ]);
        }
        catch(\Exception $e) {}

        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();
    }
    
    public function signup(
        Request $request, 
        UserValidator $validator
    ) {
        try {
            $validator->validate($request->all());

            /**
            * add emp prefix for employees
            * to prevent email conflict
            * due to shared models
            * admin users and owner/tenant accounts
            */
            $email = ($request->user_type != 'account' ? 'emp' : '') . ".{$request->email}";

            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => bcrypt($request->password),
                'user_type' => $request->user_type
            ]);
            
            return response()->json(['message' => 'success']);
            
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