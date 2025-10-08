<?php

namespace App\Models;

use App\Http\Models\Admin\Retail\RetailUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentJourneyRetailUser extends Model
{
    use HasFactory;

    public function retail_user()
    {
        return $this->belongsTo(RetailUser::class,'retail_user_id','id');
    }

}
