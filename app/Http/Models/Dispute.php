<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'description','raised_by','raised_by_status','city_id','dispute_type_id','shipments_count','status'
    ];
    public function comments(){
        return $this->hasMany('App\Http\Models\DisputeComment');
    }
    public function admins(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','raised_by','id');
    }
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }
    public function dispute_types(){
        return $this->belongsTo('App\Http\Models\DisputeType','dispute_type_id','id');
    }
    public function shipper(){
        return $this->belongsTo('App\Http\Models\Shipper\User','raised_by','id');
    }

    public function dispute_shipments() {
        return $this->hasMany('App\Http\Models\DisputeShipment');
    }
}
