<?php

namespace App\Http\Models\Admin\CargoManifest;

use Illuminate\Database\Eloquent\Model;

class V2JunctionMapping extends Model
{
    public function junctions()
    {
        return $this->hasMany(V2Junctions::class,'junction_mapping_id');
    }

    public function routes()
    {
        return $this->hasMany(V2JunctionRoutes::class,'junction_mapping_id');
    }

}
