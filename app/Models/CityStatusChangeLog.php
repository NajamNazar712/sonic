<?php

namespace App\Models;

use App\Http\Models\Admin\Admin;
use App\Http\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CityStatusChangeLog extends Model
{
    use HasFactory;

    protected $table = 'city_status_change_logs';

    protected $dates = ['changed_at'];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}
