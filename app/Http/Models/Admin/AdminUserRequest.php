<?php

namespace App\Http\Models\Admin;


use Illuminate\Database\Eloquent\Model;

class AdminUserRequest extends Model
{
    public function hubs() {
        return $this->hasMany('App\Http\Models\Admin\AdminHub', 'admin_id', 'id');
    }
}
