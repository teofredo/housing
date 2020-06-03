<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class InternetSubscription extends Base
{
    use SoftDeletes;
    
    protected $primaryKey = 'subscription_id';
    
    protected $dates = [
        'start_date', 'end_date', 'installed_at', 'cancelled_at',
        'created_at', 'updated_at', 'deleted_at'
    ];
    
    public function account()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Account', 'account_id', 'account_id');
    }
    
    public function plan()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\InternetPlan', 'plan_id', 'plan_id');
    }
}
