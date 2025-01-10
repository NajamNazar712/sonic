<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingaApiLog extends Model
{
    use HasFactory;
    protected $fillable = ['nature', 'status','details', 'shipment_id'];
}
