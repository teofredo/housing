<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Validators\UserValidator;
use App\Exceptions\ValidationException;
use App\Services\ErrorResponse;
use App\transformers\UserTransformer;

class UsersController extends Controller
{
	protected $model = User::class;
	protected $transformer = UserTransformer::class;

	public function index($id=null, Request $request)
	{
		try {
			return parent::index($id, $request);
		} catch(\Exception $e) {}

		$errorResponse = new ErrorResponse($e);

		return $errorResponse->toJson();
	}

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

    public function user(Request $request)
    {
    	// $user = $request->user();
    	// return $this->fractal->item($user, new UserTransformer);

    	dd(Auth::user());
    }
}