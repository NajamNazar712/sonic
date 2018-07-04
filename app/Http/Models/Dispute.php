<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'description','admin_id','city_id','dispute_type_id','shipments_count','status'
    ];
    public function comments(){
        return $this->hasMany('App\Http\Models\DisputeComment');
    }
    public function admins(){
        return $this->belongsTo('App\Http\Models\Admin\Admin');
    }
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }
    public function dispute_types(){
        return $this->belongsTo('App\Http\Models\DisputeType');
    }
}
