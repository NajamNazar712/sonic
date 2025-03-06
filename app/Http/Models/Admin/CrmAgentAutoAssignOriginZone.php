<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignOriginZone extends Model
{
    use HasFactory;

    public function zones() {
        return $this->belongsTo('App\Http\Models\Zone', 'origin_zone_id', 'id');
    }
}
