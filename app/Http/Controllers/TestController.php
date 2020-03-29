<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function test()
    {
        $service = new \App\Services\CurlService;
        
        $request = [
            // 'grant_type' => 'client_credentials',
            // 'client_id' => env('CLIENT_ID'),
            // 'client_secret' => env('CLIENT_SECRET'),
            'username' => 'logroniozichri@gmail.com',
            'password' => '123456789',
            // 'scope' => '*'
        ];
            
        $response = $service->httpPost('http://passport.test/api/v1/login', $request);
            
        return response($response)
            ->header('Access-Control-Allow-Origin', env('CLIENT_DOMAIN'));
    }
}
