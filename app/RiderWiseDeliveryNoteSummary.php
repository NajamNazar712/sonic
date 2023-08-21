<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RiderWiseDeliveryNoteSummary extends Model
{
    public function delivery_notes() {
        return $this->hasMany(RiderWiseDeliveryNote::class, 'rwdnsum_id');
    }
}
