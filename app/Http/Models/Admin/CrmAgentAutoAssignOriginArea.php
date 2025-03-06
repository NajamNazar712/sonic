<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignOriginArea extends Model
{
    use HasFactory;

    public function city_area() {
        return $this->belongsTo('App\Http\Models\CityArea', 'origin_area_id', 'id');
    }
}
