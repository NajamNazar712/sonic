<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class RouteManagementJunction extends Model
{
    public function route_management(){
        return $this->belongsTo('App\Http\Models\Admin\RouteManagement', 'route_management_id', 'id');
    }

    public function junction() {
        return $this->belongsTo('App\Http\Models\City', 'junction_id', 'id');
    }
}
