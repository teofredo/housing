<?php
namespace App\Listeners;

use Illuminate\Support\Facades\{
	DB,
	Auth
};
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Client;

class RevokeOldTokens
{
	public function __construct()
	{
		//
	}
	
	public function handle(AccessTokenCreated $event)
	{
	    DB::table('oauth_access_tokens')
	        ->where('id', '<>', $event->tokenId)
	        ->where('user_id', '=', $event->userId)
	        ->where('client_id', '=', $event->clientId)
	        ->delete();
	}
}