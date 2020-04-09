<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class InternetPlan extends Base
{
	use SoftDeletes;

    protected $primaryKey = 'plan_id';
    
    public function subscribers()
    {
    	//
    }
}
