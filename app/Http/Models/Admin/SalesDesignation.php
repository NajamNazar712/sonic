<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class SalesDesignation extends Model
{
    public function role() {
        return $this->belongsTo('App\Http\Models\Admin\AdminRole', 'designation', 'id');
    }
}
