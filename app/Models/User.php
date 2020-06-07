<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $guarded = [];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    protected $dates = [
        'email_verified_at', 'created_at', 'updated_at', 'deleted_at'
    ];
    
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }
    
    public function getTableColumns()
    {
        return $this->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }
}
