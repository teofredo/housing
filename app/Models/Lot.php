<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Lot extends Base
{
    use SoftDeletes;

    protected $primaryKey = 'lot_id';
    
    public function block()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Block', 'block_id', 'block_id');
    }
    
    public function resident()
    {
    	return $this->hasOne(__NAMESPACE__ . '\\Resident', 'lot_id');
    }

    public function setNameAttribute($value)
    {
    	$this->attributes['name'] = strtoupper($value);
    }
}
