<?php

namespace App\Http\Models\Shipper;

use Illuminate\Database\Eloquent\Model;

class ReturnSheetShipments extends Model
{
    protected $table = 'return_shipments_received_app';
    protected $connection = 'mysql';
}
