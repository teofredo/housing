<?php

namespace App\Models;

class Block extends Base
{
    protected $primaryKey = 'block_id';
    
    public function lots()
    {
    	return $this->hasMany(__NAMESPACE__ . '\\Lot', 'block_id');
    }
    
    public function residents()
    {
    	return $this->hasMany(__NAMESPACE__ . '\\Resident', 'block_id');
    }

    public function setNameAttribute($value)
    {
    	$this->attributes['name'] = strtoupper($value);
    }
}
