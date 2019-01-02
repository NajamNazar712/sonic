<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PettyCashStatementDetail extends Model
{
    protected $table = 'petty_cash_statement_details';
    public function petty_cash_statement() {
        return $this->belongsTo('App\Http\Models\Admin\PettyCashStatement');
    }
}
