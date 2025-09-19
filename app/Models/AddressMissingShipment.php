<?php

namespace App\Models;

use App\Http\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressMissingShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'type_name_id',
        'rider_id',
        'updated_by',
        'status',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function type()
    {
        return $this->belongsTo(AddressMissingShipmentType::class, 'type_name_id');
    }
}
