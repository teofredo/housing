<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AccessToken extends Base
{
	use SoftDeletes;
	
    public $timestamps = true;
    
    protected $fillable = ['user_id', 'access_token', 'refresh_token', 'expired_at'];
}