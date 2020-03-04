<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class OvernightOverlandReportData extends Model
{
    public function origin() {
        return $this->belongsTo('App\Http\Models\City', 'origin_id', 'id');
    }

    public function destination() {
        return $this->belongsTo('App\Http\Models\City', 'destination_id', 'id');
    }

    public function transport_mode_vendor() {
        return $this->belongsTo('App\Http\Models\TransportModeVendor', 'vendor_id', 'id');
    }

    public function shipping_mode() {
        return $this->belongsTo('App\Http\Models\ShippingMode', 'shipping_mode_id', 'id');
    }
}
