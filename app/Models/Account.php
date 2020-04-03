<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $primaryKey = 'account_id';
    
    public function resident()
    {
    	return $this->hasOne(__NAMESPACE__ . '\\Resident', 'account_id');
    }
}
