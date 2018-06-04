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
        'name', 'email', 'password','address','poc','phone','phone2','cnic','ntn_no','url','city_code','status','blacklist'
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
        return $this->belongsToMany('App\Http\Models\Product')->withTimestamps();
    }
    public function city(){
        return $this->belongsTo('App\Http\Models\CityInfo','city_code','city_code');
    }

    public function bank(){
        return $this->hasOne('App\Http\Models\Shipper\UserBankInfo');
    }
    public function shipping(){
        return $this->hasMany('App\Http\Models\Shipper\UserShippingInfo');
    }
}
