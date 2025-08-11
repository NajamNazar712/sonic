<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailShipperInfo extends Model
{
    protected $fillable = [
        'shipper_phone_no',
        'shipper_name',
        'shipper_cnic',
        'shipper_address',
        'bank_id',
        'iban',
        'account_number',
        'cheque_image',
        'cnic_front',
        'cnic_back',
        'status',
        'completed_status',
        'city_id',
        'api_token',
        'password',
        'password_reset_limit',
        'password_reset_at',
        'retail_otp',
        'otp_expire_at',
    ];

    protected $casts = [
        'password_reset_at' => 'datetime',
    ];

    public function bank() {
        return $this->belongsTo('App\Http\Models\BanksList', 'bank_id', 'id');
    }

    public function retail_city(){
        return $this->belongsTo('App\Http\Models\City','city_id', 'id');
    }
}
