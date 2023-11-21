<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'city_id','code','start','end','junction','status','route_type_id','route_code'
    ];
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }
    public function rider(){
        return $this->hasMany('App\Http\Models\Rider');
    }
    public function deliverynotes(){
        return $this->hasMany('App\Http\Models\Route');
    }
   /* public function route_type(){
        return $this->belongsTo('App\Http\Models\RouteType');
    }*/
}
