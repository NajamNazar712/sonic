<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class UserIbftChargeDetail extends Model
{
    protected $fillable = ['charges','user_id','updated_by'];
}
