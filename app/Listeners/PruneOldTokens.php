<?php
namespace App\Listeners;

use Illuminate\Support\Facades\{
	DB,
	Auth
};
use Laravel\Passport\Events\RefreshTokenCreated;
use Laravel\Passport\Client;

class PruneOldTokens
{
	public function __construct()
	{
		//
	}
	
	public function handle(RefreshTokenCreated $event)
	{
	    DB::table('oauth_refresh_tokens')
	        ->where('id', '<>', $event->refreshTokenId)
	        ->where('access_token_id', '<>', $event->accessTokenId)
	        ->delete();
	}
}