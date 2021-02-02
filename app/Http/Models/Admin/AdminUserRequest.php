<?php

namespace App\Http\Models\Admin;


use Illuminate\Database\Eloquent\Model;

class AdminUserRequest extends Model
{
    public function hub() {
        return $this->hasMany('App\Http\Models\Admin\AdminHub', 'admin_id', 'id');
    }
    public function depart() {
        return $this->belongsTo('App\Http\Models\Admin\AdminDepartment', 'department', 'id');
    }
}
