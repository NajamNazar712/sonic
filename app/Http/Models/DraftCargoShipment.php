<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DraftCargoShipment extends Model
{
    public $timestamps = false;
    public function draft_cargo() {
        return $this->belongsTo('App\Http\Models\DraftCargo');
    }
    //
}
