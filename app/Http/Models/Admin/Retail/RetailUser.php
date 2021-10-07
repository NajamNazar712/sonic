<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RetailUser extends Authenticatable
{
    protected $guard = 'retail';

    protected $fillable = [
        'name', 'password'
    ];

    public function store() {
        if($this->category == 1){
            return $this->belongsTo('App\Http\Models\Admin\Retail\RetailFranchise', 'category_id', 'id');
        }
        else{
            return $this->belongsTo('App\Http\Models\Admin\Retail\RetailTraxCenter', 'category_id', 'id');
        }
    }
}
