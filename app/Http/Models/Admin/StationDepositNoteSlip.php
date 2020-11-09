<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StationDepositNoteSlip extends Model
{
    public function bank(){
        return $this->belongsTo('App\Http\Models\Shipper\UserBankInfo', 'bank_id', 'id');
    }
}
