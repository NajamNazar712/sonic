<?php

namespace App\Http\Models\Shipper;

use Illuminate\Database\Eloquent\Model;

class UserBankInfo extends Model
{
    protected $fillable = ['user_id','bank_name','bank_branch','account_no','account_title','iban','city_code','payment_mode','payment_cycle'];
    public function user(){
        return $this->belongsTo('App\Http\Models\Shipper\User');
    }
}
