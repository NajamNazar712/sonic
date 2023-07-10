<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignHub extends Model
{
    public function hubs() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
}
