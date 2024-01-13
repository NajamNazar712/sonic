<?php

namespace App\Http\Models\Admin\CargoManifest;

use Illuminate\Database\Eloquent\Model;

class IssueSackBagOrigin extends Model
{
    protected $fillable = ['sack_bag_no', 'origin', 'user_id', 'remarks', 'type'];
}
