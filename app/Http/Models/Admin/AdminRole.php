<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
	public function department() {
		return $this->belongsTo('App\Http\Models\Admin\AdminDepartment', 'department_id', 'id');
	}

	public function module_permissions() {
		return $this->hasMany('App\Http\Models\Admin\AdminRoleModulePermission', 'role_id', 'id');
	}
}
