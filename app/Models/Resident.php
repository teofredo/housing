<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    protected $primaryKey = 'resident_id';
    
    public function lot()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Lot', 'lot_id');
    }
    
    public function account()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Account', 'account_id');
    }
}
