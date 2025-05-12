<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignOriginHub extends Model
{
    use HasFactory;

    public function hubs() {
        return $this->belongsTo('App\Http\Models\City', 'origin_hub_id', 'id');
    }
}
