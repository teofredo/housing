<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OtherCharge extends Base
{
	use SoftDeletes;

    public function account()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Account', 'account_id', 'account_id');
    }

    public function fee()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Fee', 'fee_id', 'fee_id');
    }
}
