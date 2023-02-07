<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ReturnNote extends Model
{
    protected $fillable = [
        'hub_id','rider_id','route_id','shipments_count','admin_id','updated_by', 'created_via_app', 'status'
    ];
    public function pickup_address() {
        return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo');
    }

    public function consignee_city() {
        return $this->belongsTo('App\Http\Models\City', 'consignee_city_id', 'id');
    }

    public function rider(){
    return $this->belongsTo('App\Http\Models\Rider');
}
    public function route(){
        return $this->belongsTo('App\Http\Models\Route');
    }
    public function hub(){
        return $this->belongsTo('App\Http\Models\City','hub_id');
    }
    public function user() {
        return $this->belongsTo('App\Http\Models\Shipper\User');
    }

    public function return_note_shipments() {
        return $this->hasMany('App\Http\Models\Admin\ReturnNoteShipment');
    }

    public function admin(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','admin_id');
    }

}
