<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentLedger extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'shipment_book_date',
        'particulars',
        'particular_id',
        'debit',
        'credit',
        'balance',
        'ledger_time',
        'number_of_shipments',
        'tracking_number',
        'origin',
        'destination',
        'cod_amount',
        'type_of_charges',
        'weight_charges',
        'fuel_surcharge',
        'gst',
        'net_payable',
        'reference_id'
    ];
}
