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

        $not_change_shipper_ids = [343, 1101, 1001, 1269, 1159, 167, 1133, 1271, 375, 468, 1197, 273, 405, 309, 917, 1274, 395, 1027, 442, 415, 1239, 117, 459, 994, 233, 1151, 616, 428, 1182, 967, 971, 341, 238, 486, 602, 852, 868, 1236, 987, 267, 1264, 1152, 890, 580, 566, 258, 1275, 254, 260, 1234, 222];

        $charges = \App\Http\Models\WeightCharge::whereNotIn('user_id', $not_change_shipper_ids)->get();

        foreach($charges as $charge) {
            $charge->national_charges_class_1 = '10%';
            $charge->national_charges_class_2 = '20%';
            $charge->national_charges_class_3 = '250';

            $charge->save();
        }

        $charges = \App\Http\Models\WeightCharge::whereIn('user_id', $not_change_shipper_ids)->get();

        foreach($charges as $charge) {
            $charge->national_charges_class_1 = '0%';
            $charge->national_charges_class_2 = '0%';
            $charge->national_charges_class_3 = '0%';

            $charge->save();
        }
    }
}
