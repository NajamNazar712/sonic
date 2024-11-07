<?php

namespace Database\Seeders;

use App\Http\Models\PackagingCharge;
use App\Http\Models\PackagingMaterialTypeSizes;
use Illuminate\Database\Seeder;

class UpdateOldPackagingMaterialChargesShipperWiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type_sizes = PackagingMaterialTypeSizes::whereNotNull('standard_charges');
        if($type_sizes->exists()){
            $type_sizes = $type_sizes->get();
            foreach ($type_sizes as $size){
                $packaging_charges = PackagingCharge::where('type_id', $size->type_id)->where('size_id', $size->id);
                if($packaging_charges->exists()){
                    $packaging_charges = $packaging_charges->get();
                    foreach ($packaging_charges as $packaging_charge){
                        $packaging_charge->charges = $size->standard_charges;
                        $packaging_charge->save();
                    }
                }
            }
        }
    }
}
