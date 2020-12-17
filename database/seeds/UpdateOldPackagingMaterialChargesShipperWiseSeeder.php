<?php

use App\Http\Models\PackagingCharge;
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
        $type_ids = [11];
        $packaging_charges = PackagingCharge::whereIn('type_id', $type_ids);
        if($packaging_charges->exists()){
            $packaging_charges = $packaging_charges->get();
            foreach ($packaging_charges as $packaging_charge){
                $charges = $packaging_charge->charges * 50;
                $packaging_charge->charges = $charges;
                $packaging_charge->save();
            }
        }
    }
}
