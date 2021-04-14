<?php

namespace App\Http\Models\Admin;

use App\Http\Models\City;
use Illuminate\Database\Eloquent\Model;

class PettyCashStatementDetailDraft extends Model
{
    public function hub(){
        return $this->belongsTo(City::class,'hub_id');
    }
}
