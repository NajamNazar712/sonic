<?php

use App\Http\Models\Admin\CargoManifest\CargoManifest;
use Illuminate\Database\Seeder;

class UpdateManifestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manifest_ids = array(146119,148417,148620,158336,158336,236916,236932,260776,260776,285616,285851,285882,286007,286007,287701,287731,287731,287731,287736,308831);

        foreach ($manifest_ids as $manifest_id){
            $cargo = CargoManifest::find($manifest_id);
            if($cargo){
                $cargo->status_id = 2;
                $cargo->short_received_bags = 0;
                $cargo->save();
            }
        }
    }
}
