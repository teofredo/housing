<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{
    public function signup(Request $request)
    {
    	try {
	    	$validator = $request->validate([
	    		'name' => 'required',
	    		'email' => 'email|unique:users',
	    		'password' => 'required'
	    	]);
	    	
	    	if(!$validator) {
	    		throw new \Exception($validator->errors());
	    	}
	    	
	    	$user = \App\User::create([
	    		'name' => $request->name,
	    		'email' => $request->email,
	    		'password' => bcrypt($request->password)
	    	]);
	    	
	    	return response()->json(['message' => 'success']);
	    	
	    } catch(\Exception $e) {}
	    
	    return response()->json($e);
    }
}
