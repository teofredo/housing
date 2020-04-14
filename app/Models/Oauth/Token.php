<?php

namespace App\Models\Oauth;

class Token extends \Laravel\Passport\Token
{
    protected $table = 'oauth_access_tokens_accounts';
}