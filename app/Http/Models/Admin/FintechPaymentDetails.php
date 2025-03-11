<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class FintechPaymentDetails extends Model
{
    protected $connection = 'mysql';
    protected $table = 'fintech_payment_details';

    public function trax_pay_trans(){
        return $this->belongsTo('App\Http\Models\Admin\TraxPayTransaction','trax_pay_id','id');
    }
}
