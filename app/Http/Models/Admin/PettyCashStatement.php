<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PettyCashStatement extends Model
{
    protected $table = 'petty_cash_statements';

    public function petty_cash_statement_details(){
        return $this->hasMany('App\Http\Models\Admin\PettyCashStatementDetail');
    }
    public function hub() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
}
