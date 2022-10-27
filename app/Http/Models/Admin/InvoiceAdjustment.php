<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class InvoiceAdjustment extends Model
{

    public function reasons() {
        return $this->belongsTo('App\Http\Models\Admin\InvoiceAdjustmentReasons', 'reason_id', 'id');
    }

}
