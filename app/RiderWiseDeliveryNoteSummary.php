<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RiderWiseDeliveryNoteSummary extends Model
{
    public function delivery_notes() {
        return $this->hasMany(RiderWiseDeliveryNote::class, 'rwdnsum_id');
    }

    public function delivery_note_shipments() {
        return $this->hasMany(RiderWiseDeliveryNoteShipment::class, 'rwdnsum_id');
    }
}
