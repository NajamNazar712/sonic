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
        $charges = \App\Http\Models\WeightCharge::all();
        foreach($charges as $charge)
        {
            $charge->national_charges_class_1 = '10%';
            $charge->national_charges_class_2 = '20%';
            $charge->national_charges_class_3 = '250';
            $charge->save();
        }
        $standard_charges = \App\Http\Models\Admin\StandardWeightCharge::all();
        foreach($standard_charges as $charge)
        {
            $charge->national_charges_class_1 = '10%';
            $charge->national_charges_class_2 = '20%';
            $charge->national_charges_class_3 = '250';
            $charge->save();
        }
    }
}
