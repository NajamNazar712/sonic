<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipperSegmentLogs extends Model
{
    // use HasFactory;

    protected $fillable = [
        'shipment_id',
        'segment_id',
        'sub_segment_id',
    ];
}
