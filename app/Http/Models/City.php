<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name','hub','hub_id','pickup','status'
    ];
    public function hub(){
       return $this->belongsTo(self::class, 'hub_id');
    }
    public function routes(){
        return $this->hasMany('App\Http\Models\Route');
    }
    public function riders(){
        return $this->hasMany('App\Http\Models\Rider');
    }
    public function admins(){
        return $this->hasMany('App\Http\Models\Admin\Admin');
    }
}
