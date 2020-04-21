<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Base
{
    use SoftDeletes;

    protected $primaryKey = 'block_id';
    
    public function lots()
    {
    	return $this->hasMany(__NAMESPACE__ . '\\Lot', 'block_id');
    }
    
    public function householders()
    {
    	return $this->hasMany(__NAMESPACE__ . '\\Householder', 'block_id');
    }

    public function setNameAttribute($value)
    {
    	$this->attributes['name'] = strtoupper($value);
    }
}
