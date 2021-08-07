<?php

namespace App\Http\Models\Admin\CargoManifest;

use App\Http\Models\City;
use Illuminate\Database\Eloquent\Model;

class V2Junctions extends Model
{
    public function city()
    {
        return $this->belongsTo(City::class,'junction_id');
    }
}
