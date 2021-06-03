<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class RouteManagement extends Model
{
    public function starting_point() {
        return $this->belongsTo('App\Http\Models\City', 'starting_point_id', 'id');
    }

    public function end_point() {
        return $this->belongsTo('App\Http\Models\City', 'end_point_id', 'id');
    }

    public function junctions(){
        return $this->hasMany('App\Http\Models\Admin\RouteManagementJunction');
    }
    
}


