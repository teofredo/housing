<?php

namespace App\Models;

class Adjustment extends Base
{
    public function account()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Account', 'account_id', 'account_id');
    }
}
