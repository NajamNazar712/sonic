<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RiderWiseDeliveryNote extends Model
{
    public function delivery_note_shipments() {
        return $this->hasMany(RiderWiseDeliveryNoteShipment::class, 'rwdn_id');
    }
}
