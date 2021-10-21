<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminDepartment extends Model
{
	public $timestamps = FALSE;

    public function department_head() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'department_head_id', 'id');
    }
}
