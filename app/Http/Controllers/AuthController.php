<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ErrorResponse;
use App\Exceptions\ValidationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Transformers\UserTransformer;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        try {
            $resource = Auth::user();
            $resource = $this->fractal->item($resource, new UserTransformer)->get();
            
            return response($resource);
        }
        catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();
    }

    public function logout(Request $request)
    {
        try {
            Auth::user()
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