<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Oauth\Token;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        // Route::group(['middleware' => 'cors'], function(){
            Passport::routes();
            Passport::tokensExpireIn(Carbon::now()->addHours(5)); 
        // }); 
        
               
        
        // Route::group(['middleware' => 'auth.provider'], function(){
        //     $provider = $this->app->request->provider ?? 'users';
            
            // Passport::routes();
            // Passport::tokensExpireIn(Carbon::now()->addHours(5));
            
        //     if($provider == 'accounts') {
        //         Passport::useTokenModel(Token::class);   
        //     }
        // });
            
            
        // Passport::tokensCan();
    }
}