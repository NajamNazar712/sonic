<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PettyCashAccountHead extends Model
{
    protected $table = 'petty_cash_account_heads';

    public function account_titles(){
        return $this->belongsToMany('App\Http\Models\Admin\PettyCashAccountTitle', 'petty_cash_account_head_account_title');
    }
}
