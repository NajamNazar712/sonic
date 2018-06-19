<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    protected $fillable=[
        'delivery_note_id','hub_id','rider_id','route_id','shipments_count','admin_id','total_cod_amount'
    ];

    public function rider(){
        return $this->belongsTo('App\Http\Models\Rider');
    }
    public function route(){
        return $this->belongsTo('App\Http\Models\Route');
    }
    public function hub(){
        return $this->belongsTo('App\Http\Models\City','hub_id');
    }
}
