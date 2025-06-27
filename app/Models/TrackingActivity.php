<?php

namespace App\Models;

use App\Http\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'shipment_id',
        'admin_id'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

}
