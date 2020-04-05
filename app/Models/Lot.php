<?php

namespace App\Models;

class Lot extends Base
{
    protected $primaryKey = 'lot_id';
    
    public function block()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Block', 'block_id', 'block_id');
    }
    
    public function resident()
    {
    	return $this->hasOne(__NAMESPACE__ . '\\Resident', 'lot_id');
    }
}
