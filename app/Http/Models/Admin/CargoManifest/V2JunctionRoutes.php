<?php

namespace App\Http\Models\Admin\CargoManifest;

use Illuminate\Database\Eloquent\Model;

class V2JunctionRoutes extends Model
{
    public function vehicles()
    {
        return $this->hasMany(V2JunctionVehicles::class,'junction_route_id');
    }
}
