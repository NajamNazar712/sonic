<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;
    public function users(){
        return $this->belongsToMany('App\Http\Models\User');
    }
}
