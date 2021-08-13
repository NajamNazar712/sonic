<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    protected $fillable=[
        'delivery_note_id','hub_id','rider_id','route_id','shipments_count','admin_id','updated_by','total_cod_amount','received_total_amount','expense','net_amount','remarks','status','dncc_status','last_updated_at','status_updated_at','status_verified_at','cash_collected_by','cash_collected_at','password','special_rider_name','special_rider_phone','special_rider','ordering','pending_for_verification_at'
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

    public function delivery_note_undelivered_shipments() {
        return $this->hasMany('App\Http\Models\Admin\DeliveryNoteShipment')->where('status', 1)->select('shipment_id');
    }

    public function delivery_note_station_deposit_note() {
        return $this->hasOne('App\Http\Models\Admin\DeliveryNoteStationDepositNote', 'delivery_note_id', 'id');
    }

    public function delivery_note_fake_status_shipments() {
        return $this->hasMany('App\Http\Models\Admin\DeliveryNoteShipment', 'delivery_note_id', 'id')->where('fake_status', 1)->select('shipment_id');
    }

    public function admin(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','admin_id');
    }

}
