<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\{
    AccountService,
    OtherChargeService,
    PaymentService,
    MonthlyDueService
};
use Carbon\Carbon;

class TestController extends Controller
{
    public function test()
    {
        $result = getPaymentDue();
        // $result = MonthlyDueService::ins()->getSummary('04/2020', 5)->toArray();
        // $result = MonthlyDueService::ins()->generateMonthDue('04/2020');
        // $result = PaymentService::ins()->initPayments('04/2020');
        
        // $result = MonthlyDueService::ins()->generateMonthDue();
        // $account = AccountService::ins()->find(1);
        // $result = MonthlyDueService::ins()->summarize($account);
        dd($result);

        $result = \App\Models\Householder::find(1)->with('account')->get()->toJson();
        echo $result;
        die;
        // $result = \App\Models\Block::with('lots')->get()->toJson();
        // print_r($result);
        // die;
        
        
        echo bcrypt('123456789'); 
        die;
        
        MonthlyDueService::ins()
            ->setDueDate(Carbon::parse('2020-04-30'))
            ->generateAdjustments();

        // PaymentService::ins()->initPayments(Carbon::parse('2020-04-30'));
        


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
