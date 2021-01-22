<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderReturnDeliveryActionLog extends Model
{
    public function return_note_id() {
        return $this->belongsTo('App\Http\Models\Admin\ReturnNote', 'return_note_id', 'id');
    }

    public function return_note_shipment_id() {
        return $this->belongsTo('App\Http\Models\Admin\ReturnNoteShipment', 'return_note_shipment_id', 'id');
    }

    public function type_id() {
        return $this->belongsTo('App\Http\Models\Rider\DeliveryAction', 'type_id', 'id');
    }
}
