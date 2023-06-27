<?php

namespace App\Http\Models\Admin\CargoManifest;

use Illuminate\Database\Eloquent\Model;

class CargoManifestBagShipments extends Model
{
    public function bag()
    {
        return $this->belongsTo('App\Http\Models\Admin\CargoManifest\CargoManifestBag','cargo_manifest_bag_id');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Http\Models\Shipment','shipment_id');
    }

    public function manifestBag_latest() {
        return $this->belongsTo('App\Http\Models\Admin\CargoManifest\ManifestBag','cargo_manifest_bag_id','cargo_manifest_bag_id')->orderBy('created_at','desc');
    }

}
