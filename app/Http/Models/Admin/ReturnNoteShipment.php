<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ReturnNoteShipment extends Model
{
    public $timestamps = false;
    protected $fillable = ['return_note_id','shipment_id'];

    public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}
}
