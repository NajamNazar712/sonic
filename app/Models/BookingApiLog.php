<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingApiLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'endpoint',
        'payload',
        'ip',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
