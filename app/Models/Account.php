<?php

namespace App\Models;

class Account extends Base
{
    protected $primaryKey = 'account_id';
    
    public function householder()
    {
    	return $this->hasOne(__NAMESPACE__ . '\\Householder', 'account_id');
    }

    public function setAccountNameAttribute($value)
    {
    	$this->attributes['account_name'] = strtoupper($value);
    }
}
