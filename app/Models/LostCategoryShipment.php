<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostCategoryShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'type',
    ];

   public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}

}
