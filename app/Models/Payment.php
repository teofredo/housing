<?php

namespace App\Models;

class Payment extends Base
{
    protected $primaryKey = 'payment_id';
    
    protected $dates = [
    	'paid_at', 'created_at', 'updated_at'
    ];

    public function account()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Account', 'account_id', 'account_id');
    }

    public function setOrNoAttribute($value)
    {
    	$this->attributes['or_no'] = strtoupper($value);
    }
}
