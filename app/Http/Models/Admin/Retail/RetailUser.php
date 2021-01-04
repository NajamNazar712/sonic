<?php

namespace App\http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RetailUser extends Authenticatable
{
    protected $guard = 'retail';

    protected $fillable = [
        'name', 'password'
    ];
}
