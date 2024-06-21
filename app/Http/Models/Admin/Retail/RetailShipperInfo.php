<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailShipperInfo extends Model
{

    public function bank() {
        return $this->belongsTo('App\Http\Models\BanksList', 'bank_id', 'id');
    }


    
    protected $table = 'retail_shipper_infos';

    // If your table doesn't have timestamps columns
    public $timestamps = false;

    // Specify the columns that are fillable
    protected $fillable = [
        'shipper_phone_no',
        'shipper_name',
        'shipper_cnic',
        'shipper_address',
        'pin',
    ];
}
