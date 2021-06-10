<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{

    public function vehicle_type(){
        return $this->belongsTo('App\Http\Models\Admin\VehicleType', 'vehicle_type_id', 'id');
    }


    
}
