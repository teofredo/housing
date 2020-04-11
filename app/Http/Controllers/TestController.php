<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\{
    AccountService,
    OtherChargeService,
    PaymentService
};
use Carbon\Carbon;

class TestController extends Controller
{
    public function test()
    {
        PaymentService::ins()->copyAccountsToPayments(Carbon::parse('2020-04-30'));
        die;

        // $result = AccountService::ins()
        //     ->getModel()
        //     ->whereRaw('accounts.account_id NOT IN(select account_id from payments WHERE due_date = ?)', ['2020-05-30'])
        //     ->get();

        // $result = AccountService::ins()
        //     ->findBy('status', 'active')
        //     ->each(function($model){
        //         $model['payment'] = PaymentService::ins()
        //             ->getModel()
        //             ->where('account_id', $model->account_id)
        //             ->orderBy('due_date', 'desc')
        //             ->first();
        //     });

        // $result = OtherChargeService::ins()
        //     ->getModel()
        //     ->where('due_date', '2020-04-30')
        //     ->join('fees', function($join){
        //         $join->on('other_charges.fee_id', '=', 'fees.fee_id')
        //             ->where('fees.other_fee', 0);
        //     })
        //     ->get();

        echo $result->toJson();
        die;


        /**
        *
        */
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
