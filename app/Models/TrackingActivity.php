<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'shipment_id',
        'admin_id'
    ];

}
