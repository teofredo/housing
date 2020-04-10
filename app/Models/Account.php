<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Base
{
	use SoftDeletes;

    protected $primaryKey = 'account_id';
    
    public function householder()
    {
    	return $this->hasOne(__NAMESPACE__ . '\\Householder', 'account_id');
    }

    public function otherCharges()
    {
        return $this->hasMany(__NAMESPACE__ . '\\OtherCharge', 'account_id', 'account_id');
    }

    public function setAccountNameAttribute($value)
    {
    	$this->attributes['account_name'] = strtoupper($value);
    }
}
