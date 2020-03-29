<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\ValidationException;
use App\Validators\UserValidator;

use App\Services\{
	AuthApiService,
	ErrorResponse
};

use App\Models\{
	User,
	AccessToken
};

use Illuminate\Support\Carbon;

class UsersController extends Controller
{
	public function signup(
    	Request $request, 
    	UserValidator $validator
    ) {
    	try {
	    	$validator->validate($request->all());
	    	
	    	$user = User::create([
	    		'name' => $request->name,
	    		'email' => $request->email,
	    		'password' => bcrypt($request->password)
	    	]);
	    	
	    	return response()->json(['message' => 'success']);
	    	
	    } catch(\Exception $e) {}
	    
	    $errorResponse = new ErrorResponse($e);
	    
	    return $errorResponse->toJson();
    }
    
    public function login(Request $request) 
    {
    	try {
    		if($request->session()->has('user')) {
    			throw new \Exception('user already logged in');
    		}
    		
    		$data = $request->all();
            
            $data = array_merge($data, [
                'grant_type' => 'password',
            ]);
            
            $authApiService = new AuthApiService;
            
            $response = $authApiService
                ->setReqData($data)
                ->getToken();
                
            $data = json_decode($response);
            
            //get user from response access_token  
            $user = $this->getUserByAccessToken($authApiService, $data->access_token ?? '');
            
            //save token to db
            $token = AccessToken::create([
            	'user_id' => $user->user_id,
            	'access_token' => $data->access_token,
            	'refresh_token' => $data->refresh_token,
            	'expired_at' => Carbon::createFromTimestamp(time() + $data->expires_in)
            ]);
            
            //sessions
            $request->session()->put('user', [
            	'data' => (array) $user,
            	'tokens' => [
            		'access_token' => $token->access_token,
            		'refresh_token' => $token->refresh_token
            	]
            ]);
                
            return response($response, 200);
        } 
        catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();
    }
    
    private function getUserByAccessToken($authApiService, $accessToken)
    {
    	try {	        
	        $endpoint = env('OAUTH_URL') . '/api/v1/user';
	        $request = [];
	        $headers = ["Authorization: Bearer {$accessToken}"];
	        
	        $response = $authApiService->httpGet($endpoint, $request, $headers);
	        $response = json_decode($response, true);
	        if(isset($response['error'])) {
	        	throw new \Exception('Unauthenticated');
	        }
	        
	        if(empty($response['id'])) {
	        	throw new \Exception('Unauthenticated');	
	        }
	        
	        return new User($response);
	        
	    } catch(\Exception $e) {}
	    
	    throw $e;
    }
}