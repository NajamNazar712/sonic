<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ChangeShipmentAmountLog extends Model
{
    protected $fillable = [
        'shipment_id','old_amount','new_amount','admin_id', 'remarks'
    ];
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }
    public function shipper() {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
    }
}
