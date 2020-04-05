<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AccessToken extends Base
{
	use SoftDeletes;
	
    public $timestamps = true;
}