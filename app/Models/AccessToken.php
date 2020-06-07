<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AccessToken extends Base
{
	use SoftDeletes;
	
    public $timestamps = true;
    
    protected $dates = [
    	'expired_at', 'created_at', 'updated_at'
    ];
}