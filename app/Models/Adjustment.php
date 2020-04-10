<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Adjustment extends Base
{
	use SoftDeletes;

    public function account()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Account', 'account_id', 'account_id');
    }
}
