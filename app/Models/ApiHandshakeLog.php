<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiHandshakeLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_name',
        'endpoint',
        'method',
        'reference_id',
        'request_payload',
        'response_payload',
        'response_status',
        'status',
        'error_message',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];
}
