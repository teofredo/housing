<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Base
{
	use SoftDeletes;
	
    protected $primaryKey = 'fee_id';
}
