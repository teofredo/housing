<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Validators\UserValidator;
use App\Exceptions\ValidationException;
use App\Services\ErrorResponse;
use App\Services\AuthApiService;

class AuthController extends Controller
{
    public function login(
        Request $request,
        AuthApiService $authApiService
    ) {
        try {
            $data = $request->all();
            
            $data = array_merge($data, [
                'grant_type' => 'password',
            ]);
            
            $response = $authApiService
                ->setReqData($data)
                ->getToken();
                
            return response($response, 200)
                ->header('Access-Control-Allow-Origin', 'passport.test')
                ->header('Access-Control-Allow-Methods', 'POST');
        } 
        catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();
    }
    
    public function user(Request $request)
    {
        try {
            return Auth::user();
        }
        catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();
    }
}
