<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Soa extends Base
{
    use SoftDeletes;

    protected $table = 'soa';

    protected $primaryKey = 'soa_id';

    public function account()
    {
    	return $this->hasOne(__NAMESPACE__ . '\\Account', 'account_id');
    }  
}
