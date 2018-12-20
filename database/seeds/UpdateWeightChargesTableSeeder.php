<?php

use Illuminate\Database\Seeder;

class UpdateWeightChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $standard_charges = \App\Http\Models\Admin\StandardWeightCharge::all();

        foreach($standard_charges as $charge) {
            $charge->national_charges_class_1 = '10%';
            $charge->national_charges_class_2 = '20%';
            $charge->national_charges_class_3 = '250';

            $charge->save();
        }

        $not_change_shipper_ids = [1274, 459, 1159, 167, 117, 438, 233, 238, 341, 890, 446, 971, 1269, 1001, 273, 343, 309, 1101, 1133, 442, 405, 1239, 1197, 1264, 593, 994, 1245, 375, 616];

        $charges = \App\Http\Models\WeightCharge::whereNotIn('user_id', $not_change_shipper_ids)->get();

        foreach($charges as $charge) {
            $charge->national_charges_class_1 = '10%';
            $charge->national_charges_class_2 = '20%';
            $charge->national_charges_class_3 = '250';

            $charge->save();
        }

        $charges = \App\Http\Models\WeightCharge::whereIn('user_id', $not_change_shipper_ids)->get();

        foreach($charges as $charge) {
            $charge->national_charges_class_1 = '0';
            $charge->national_charges_class_2 = '0';
            $charge->national_charges_class_3 = '0';

            $charge->save();
        }
    }
}
