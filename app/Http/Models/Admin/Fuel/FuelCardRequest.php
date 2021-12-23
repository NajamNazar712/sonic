<?php

namespace App\Http\Models\Admin\Fuel;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Rider;
use Illuminate\Database\Eloquent\Model;

class FuelCardRequest extends Model
{
    public function admins() {
        return $this->belongsTo(Admin::class, 'card_holder_id', 'id');
    }

    public function riders() {
        return $this->belongsTo(Rider::class, 'card_holder_id', 'id');
    }

    public function fleet() {
        return $this->belongsTo(FleetVehicle::class, 'card_holder_id', 'id');
    }
}
