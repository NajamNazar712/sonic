<?php

namespace App\Http\Models\Shipper;

use Illuminate\Foundation\Auth\User as Authenticatable;

class SubstituteUser extends Authenticatable
{
	protected $guard = 'substitute_users';
    protected $fillable = ['name', 'email', 'password', 'phone_number', 'cnic', 'status'];
    protected $hidden = ['password', 'remember_token'];

    public function permissions() {
        return $this->hasMany('App\Http\Models\Shipper\SubstituteUserPermission', 'substitute_user_id', 'id');
    }

    public function shipper() {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
    }
}
