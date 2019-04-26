<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PettyCashStatementDetail extends Model
{
    protected $table = 'petty_cash_statement_details';

    protected $fillable = [
        'updated_by','status'
    ];

    public function petty_cash_statement() {
        return $this->belongsTo('App\Http\Models\Admin\PettyCashStatement');
    }
    public function heads(){
        return $this->belongsTo('App\Http\Models\Admin\PettyCashAccountHead', 'account_head_id', 'id');
    }
    public function titles(){
        return $this->belongsTo('App\Http\Models\Admin\PettyCashAccountTitle', 'account_title_id', 'id');
    }
    public function location() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
}
