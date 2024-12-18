<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CargoManifestBagRemarks extends Model
{
    // Format dates as 'YYYY-MM-DD HH:mm:ss'
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
