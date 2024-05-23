<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LostShipmentStatusCount extends Model
{
    protected $fillable = [
        'shipment_id',
        'approval_count',
        'lost_count',
        'rejection_count',
        'cleared',
    ];
}
