<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'city_id','code','start','end','junction','status'
    ];
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }
}
