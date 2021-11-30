<?php

namespace App\Http\Models\Shipper;
use App\Http\Models\CityInfo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','address','poc','phone','phone2','cnic','ntn_no','strn_no','url','city_id','status','blacklist', 'api_token','rates_added_by','rates_added_at','rates_approved_at','rates_rejected_at','rates_rejected_by','rates_updated_by','rates_authorized_by','account_activated_by','activated_at','reactivated_at','product_id','rate_status','account_type_id','default_shipping_mode','average_shipments','reference_id','average_shipment_duration_id','other_product_name','brand_name','segment_id','lead_id','territory_id','rcp_tat_option_id','rcp_tat_updated_by','rcp_tat_updated_at','new_rate_type_id','rate_type_id_status','agreement_signed','sub_segment_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function products(){
        return $this->belongsTo('App\Http\Models\Product', 'product_id', 'id');
    }
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }

    public function bank(){
        return $this->hasMany('App\Http\Models\Shipper\UserBankInfo');
    }
    public function shipping(){
        return $this->hasMany('App\Http\Models\Shipper\UserShippingInfo');
    }
    public function sales_person(){
        return $this->hasMany('App\Http\Models\Admin\SalePersonTag');
    }
    public function account_type(){
        return $this->belongsTo('App\Http\Models\AccountType');
    }
    public function payment_cycle(){
        return $this->belongsTo('App\Http\Models\PaymentCycle');
    }
    public function open_parcel(){
        return $this->hasOne('App\Http\Models\Shipper\OpenParcelHistory');
    }
    public function segment()
    {
        return $this->belongsTo('App\Http\Models\Segment', 'segment_id', 'id');
    }
    public function sub_segment()
    {
        return $this->belongsTo('App\Http\Models\SubCategorySegment', 'sub_segment_id', 'id');
    }
    
    public function packaging_materails()
    {
        return $this->hasMany('App\Http\Models\ShipperPackagingMaterailType','shipper_id','id');
    }
    public function return(){
        return $this->hasMany('App\Http\Models\Shipper\UserReturnInfo');
    }

    public function dws_charges(){
        return $this->hasMany('App\Http\Models\DwsWeightCharges');
    }
}
