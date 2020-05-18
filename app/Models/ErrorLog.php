<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
	protected $primaryKey = 'log_id';

    public $timestamps = true;

    protected $guarded = [];
}