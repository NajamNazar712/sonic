<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivingSheetReceived extends Model
{
	protected $table = 'receiving_sheet_received';
    protected $primaryKey = 'shipment_id';
    public $timestamps = FALSE;
}
