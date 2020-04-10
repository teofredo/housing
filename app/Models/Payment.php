<?php

namespace App\Models;

class Payment extends Base
{
    protected $primaryKey = 'payment_id';

    public function account()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Account', 'account_id', 'account_id');
    }
}
