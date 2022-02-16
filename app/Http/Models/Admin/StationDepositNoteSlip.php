<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StationDepositNoteSlip extends Model
{
    public function bank(){
        return $this->belongsTo('App\Http\Models\BanksList', 'bank_id', 'id');
    }

    public function uploaded_by_admin(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','uploaded_by','id');
    }
}
