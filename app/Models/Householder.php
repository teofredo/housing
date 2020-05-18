<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Householder extends Base
{
	use SoftDeletes;

    protected $primaryKey = 'householder_id';
    
    public function lot()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Lot', 'lot_id', 'lot_id');
    }
    
    public function account()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Account', 'account_id', 'account_id');
    }
    
    /**
    * Mutators
    */
    public function setHouseNoAttribute($value)
    {
        $value = preg_replace(['/\s+/', '/-+/'], '', $value);
        return $this->attributes['house_no'] = $value;
    }
    
    public function setWaterMeterNoAttribute($value)
    {
        $value = preg_replace(['/\s+/', '/-+/'], '', $value);
        return $this->attributes['water_meter_no'] = $value;
    }
}
