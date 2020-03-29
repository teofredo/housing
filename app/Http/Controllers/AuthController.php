<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\ValidationException;
use App\Services\ErrorResponse;

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
}