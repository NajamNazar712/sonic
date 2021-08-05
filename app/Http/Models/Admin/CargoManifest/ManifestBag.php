<?php

namespace App\Http\Models\Admin\CargoManifest;

use Illuminate\Database\Eloquent\Model;

class ManifestBag extends Model
{

    protected $fillable = ['status'];
    public function bag() {
        return $this->belongsTo('App\Http\Models\Admin\CargoManifest\CargoManifestBag', 'cargo_manifest_bag_id', 'id');
    }

    public function cargo_manifest() {
        return $this->belongsTo('App\Http\Models\Admin\CargoManifest\CargoManifest', 'cargo_manifest_id', 'id');
    }
}
