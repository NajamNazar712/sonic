<?php

namespace App\Http\Models\Admin\MasterCargo;

use Illuminate\Database\Eloquent\Model;

class MasterCargoBag extends Model
{

    protected $table = 'master_cargo_bags';
    public function bag() {
        return $this->belongsTo('App\Http\Models\Admin\MasterCargo\Bag', 'bag_id', 'id');
    }

    public function master_cargo() {
        return $this->belongsTo('App\Http\Models\Admin\MasterCargo\MasterCargo', 'master_cargo_id', 'id');
    }
}
