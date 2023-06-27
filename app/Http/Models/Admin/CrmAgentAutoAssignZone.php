<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignZone extends Model
{
    public function zones() {
        return $this->belongsTo('App\Http\Models\Zone', 'zone_id', 'id');
    }
}
