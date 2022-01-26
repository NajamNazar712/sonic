<?php

namespace App\Http\Models\Admin\CargoManifest;

use Illuminate\Database\Eloquent\Model;

class CargoManifestDraftBags extends Model
{
    protected $fillable = ['bag_id','seal_number','shipments_count','origin_id','destination_id','added_by'];

    public function origin() {
        return $this->belongsTo('App\Http\Models\City', 'origin_id', 'id');
    }

    public function destination() {
        return $this->belongsTo('App\Http\Models\City', 'destination_id', 'id');
    }

}
