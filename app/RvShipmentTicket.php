<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RvShipmentTicket extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    protected $dates = ['deleted_at','updated_at'];

    public function status_reason()
    {
        return $this->belongsTo('App\Http\Models\ShipmentStatusReason', 'shipment_status_reason_id', 'id');
    }

}
