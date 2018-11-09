<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class MisroutedHistory extends Model
{
    protected $table = 'misrouted_history';
    protected $fillable = ['shipment_id','old_consignee_city_id','old_consignee_name','old_consignee_address','old_consignee_phone_number_1','old_consignee_phone_number_2','old_consignee_email','new_consignee_city_id','new_consignee_name','new_consignee_address','new_consignee_phone_number_1','new_consignee_phone_number_2','new_consignee_email','admin_id'];
    public function old_consignee_city() {
        return $this->belongsTo('App\Http\Models\City', 'old_consignee_city_id', 'id');
    }
    public function new_consignee_city() {
        return $this->belongsTo('App\Http\Models\City', 'new_consignee_city_id', 'id');
    }

}
