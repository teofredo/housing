<?php
namespace App\Models;

class InternetSubscription extends Base
{
    protected $primaryKey = 'subscription_id';
    
    public function account()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\Account', 'account_id', 'account_id');
    }
    
    public function plan()
    {
    	return $this->belongsTo(__NAMESPACE__ . '\\InternetPlan', 'plan_id', 'plan_id');
    }
}
