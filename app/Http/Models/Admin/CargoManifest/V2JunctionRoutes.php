<?php

namespace App\Http\Models\Admin\CargoManifest;

use App\Http\Models\City;
use Illuminate\Database\Eloquent\Model;

class V2JunctionRoutes extends Model
{
    public function vehicles()
    {
        return $this->hasMany(V2JunctionVehicles::class,'junction_route_id');
    }

    public function starting()
    {
        return $this->belongsTo(City::class,'starting_hub_id');
    }

    public function ending()
    {
        return $this->belongsTo(City::class,'ending_hub_id');
    }
}
