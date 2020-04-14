<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Authenticatable
{
	use HasApiTokens, Notifiable, SoftDeletes;

    protected $primaryKey = 'account_id';
    
    protected $guarded = [];
    
    public function householder()
    {
    	return $this->hasOne(__NAMESPACE__ . '\\Householder', 'account_id');
    }

    public function otherCharges()
    {
        return $this->hasMany(__NAMESPACE__ . '\\OtherCharge', 'account_id', 'account_id');
    }

    public function adjustments()
    {
        return $this->hasMany(__NAMESPACE__ . '\\Adjustment', 'account_id', 'account_id');
    }

    public function setAccountNameAttribute($value)
    {
    	$this->attributes['account_name'] = strtoupper($value);
    }
    
    public function getTableColumns()
    {
        return $this->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }
}
