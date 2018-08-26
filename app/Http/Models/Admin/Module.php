<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
	public function permissions() {
		return $this->hasMany('App\Http\Models\Admin\ModulePermission');
	}
}
