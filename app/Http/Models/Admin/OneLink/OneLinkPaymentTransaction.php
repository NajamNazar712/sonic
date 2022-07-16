<?php

namespace App\Http\Models\Admin\OneLink;

use Illuminate\Database\Eloquent\Model;

class OneLinkPaymentTransaction extends Model
{
    //
    protected $guarded = [];

    public function shipment_data() {
		return $this->belongsTo('App\Http\Models\Shipment','shipment_id', 'id');
	}
}
