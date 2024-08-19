<?php

namespace App\Http\Models\Handover;

use Illuminate\Database\Eloquent\Model;

class ExcessHandoverShipment extends Model
{
    protected $fillable = [
        'handover_id',
        'bag_number',
        'shipment_ids',
        'excess_shipment'
    ];
}
