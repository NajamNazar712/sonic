<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingApiLogArchive extends Model
{
    use HasFactory;

    protected $table = 'booking_api_log_archives';

    protected $fillable = [
        'user_id',
        'shipment_id',
        'endpoint',
        'payload',
        'ip',
        'original_created_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'original_created_at' => 'datetime',
    ];
}
