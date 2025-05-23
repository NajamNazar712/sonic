<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonCodShipmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'trax_id',
        'employee_name',
        'employee_designation',
        'shipment_otp'
    ];

    // Format dates as 'YYYY-MM-DD HH:mm:ss'
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
