<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingaApiLog extends Model
{
    use HasFactory;
    protected $fillable = ['nature', 'status','details', 'shipment_id','request_id', 'nature_id'];

     // Format dates as 'YYYY-MM-DD HH:mm:ss'
     protected function serializeDate(\DateTimeInterface $date)
     {
         return $date->format('Y-m-d H:i:s');
     }
}
