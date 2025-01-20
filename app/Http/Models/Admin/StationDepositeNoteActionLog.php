<?php

namespace app\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StationDepositeNoteActionLog extends Model
{
    public function updated_by(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','admin_id','id');
    }

    public function previous_bank()
    {
        return $this->belongsTo('App\Http\Models\BanksList','previous_bank_id');
    }

    public function new_bank()
    {
        return $this->belongsTo('App\Http\Models\BanksList','new_bank_id');
    }
}
