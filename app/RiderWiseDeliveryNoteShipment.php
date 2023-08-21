<?php

namespace App;

use App\Http\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class RiderWiseDeliveryNoteShipment extends Model
{
    public function shipment() {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }
}
