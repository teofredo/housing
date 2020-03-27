<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
    	$service = new \App\Services\CurlService;
    	
    	$request = [
    		'grant_type' => 'client_credentials',
    		'client_id' => env('CLIENT_ID'),
    		'client_secret' => env('CLIENT_SECRET'),
    		'scope' => '*'
    	];
    	
    	$response = $service->httpPost('http://passport.test/oauth/token', $request);
    	
    	dd($response);
    }
}
