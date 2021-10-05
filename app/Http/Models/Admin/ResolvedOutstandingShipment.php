<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ResolvedOutstandingShipment extends Model
{
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'resolved_by', 'id');
    }
}
