<?php

namespace App\Http\Models\Shipper;

use Illuminate\Database\Eloquent\Model;

class UserBankInfo extends Model
{
    protected $fillable = ['user_id','bank_name','bank_branch','account_no','account_title','iban','city_id','payment_cycle','invoicing_cycle_id','generation_date','billing_person_name','billing_person_phone', 'billing_person_email','billing_address'];

    public function user(){
        return $this->belongsTo('App\Http\Models\Shipper\User');
    }

    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }

    public function bank() {
        return $this->belongsTo('App\Http\Models\BanksList', 'bank_name', 'id');
    }
}
