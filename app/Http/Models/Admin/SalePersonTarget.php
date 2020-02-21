<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class SalePersonTarget extends Model
{
    public function person() {
		return $this->belongsTo('App\Http\Models\Admin\Admin', 'sales_person_id', 'id');
	}
}
