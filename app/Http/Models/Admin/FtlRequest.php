<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class FtlRequest extends Model
{
    public function additional_cost()
    {
        return $this->hasMany(FtlRequestAdditionalCost::class);
    }
}
