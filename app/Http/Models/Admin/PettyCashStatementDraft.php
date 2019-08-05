<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PettyCashStatementDraft extends Model
{
    public function petty_cash_statement_draft_details(){
        return $this->hasMany('App\Http\Models\Admin\PettyCashStatementDetailDraft');
    }
}
