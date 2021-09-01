<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SaleTierTag extends Model
{
    public function poc_admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'poc', 'id');
    }
    public function kam_admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'kam', 'id');
    }
    public function ref_admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'ref', 'id');
    }
}
