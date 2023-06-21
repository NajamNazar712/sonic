<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{

    public function vehicle_type(){
        return $this->belongsTo('App\Http\Models\Admin\VehicleType', 'vehicle_type_id', 'id');
    }
    public function driver(){
        return $this->belongsTo('App\Http\Models\FleetDriver', 'driver_id', 'id');
    }
    public function vendor(){
        return $this->belongsTo('App\Http\Models\FleetDriver', 'vendor_id', 'id');
    }


    
}
