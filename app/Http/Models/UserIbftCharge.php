<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class UserIbftCharge extends Model
{
    protected $fillable = ['current_charges','user_id','updated_by'];
}
