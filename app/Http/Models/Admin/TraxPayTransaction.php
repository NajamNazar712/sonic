<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TraxPayTransaction extends Model
{
    protected $table      ="trax_pay_transactions";
    protected $connection = 'mysql';



    public function payment_name() {
        return $this->belongsTo('App\Http\Models\Admin\TraxPaymentName', 'payment_name_id', 'id');
    }
}
