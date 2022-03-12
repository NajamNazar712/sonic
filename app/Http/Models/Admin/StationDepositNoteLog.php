<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StationDepositNoteLog extends Model
{
    public function updated_by(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','admin_id','id');
    }
}
