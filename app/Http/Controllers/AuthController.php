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
            $response = $authApiService
                ->setReqData($request->all())
                ->getRequestToken();
                
            return response()
                ->json($response)
                ->header('Access-Control-Allow-Origin', 'passport.test')
                ->header('Access-Control-Allow-Methods', 'POST');
        } 
        catch(\Exception $e) {}
        catch(\GuzzleHttp\Exception\BadResponseException $e) {}
        
        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();
    }
}
