<?php

use Illuminate\Database\Seeder;

class UpdateWalkInReturnChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $walkin_return_charges = DB::table('walk_in_standard_weight_charges')->get();
        foreach ($walkin_return_charges as $walkin_return_charge) {
            DB::table('walk_in_standard_weight_charges')->where('id',$walkin_return_charge->id)->update(['national_charges_class_1' => 0, 'national_charges_class_2' => 0, 'national_charges_class_3' => 0]);
        }
    }
}
