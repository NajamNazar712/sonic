<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    protected $fillable=[
        'delivery_note_id','hub_id','rider_id','route_id','shipments_count','admin_id','updated_by','total_cod_amount','received_total_amount','expense','net_amount','remarks','status','dncc_status'
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

    public function delivery_note_shipments() {
        return $this->hasMany('App\Http\Models\Admin\DeliveryNoteShipment');
    }
}
