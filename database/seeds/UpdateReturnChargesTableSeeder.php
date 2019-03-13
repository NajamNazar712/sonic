<?php

use Illuminate\Database\Seeder;

class UpdateReturnChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $standard_return_charges = DB::table('standard_return_charges')->get();
        foreach ($standard_return_charges as $standard_return_charge) {
            DB::table('standard_return_charges')->where('id',$standard_return_charge->id)->update(['national_charges_class_1' => '100%', 'national_charges_class_2' => '100%', 'national_charges_class_3' => '100%']);
        }

        $return_charges = DB::table('return_charges')->get();
        foreach ($return_charges as $return_charge) {
            DB::table('return_charges')->where('id',$return_charge->id)->update(['national_charges_class_1' => '100%', 'national_charges_class_2' => '100%', 'national_charges_class_3' => '100%']);
        }

        $history_return_charges = DB::table('history_return_charges')->get();
        foreach ($history_return_charges as $history_return_charge) {
            DB::table('history_return_charges')->where('id',$history_return_charge->id)->update(['national_charges_class_1' => '100%', 'national_charges_class_2' => '100%', 'national_charges_class_3' => '100%']);
        }

        $history_corporate_return_charges = DB::table('history_corporate_return_charges')->get();
        foreach ($history_corporate_return_charges as $history_corporate_return_charge) {
            DB::table('history_corporate_return_charges')->where('id',$history_corporate_return_charge->id)->update(['national_charges_class_1' => '100%', 'national_charges_class_2' => '100%', 'national_charges_class_3' => '100%']);
        }

        $pending_return_charges = DB::table('pending_return_charges')->get();
        foreach ($pending_return_charges as $pending_return_charge) {
            DB::table('pending_return_charges')->where('id',$pending_return_charge->id)->update(['national_charges_class_1' => '100%', 'national_charges_class_2' => '100%', 'national_charges_class_3' => '100%']);
        }

        $pending_corporate_return_charges = DB::table('pending_corporate_return_charges')->get();
        foreach ($pending_corporate_return_charges as $pending_corporate_return_charge) {
            DB::table('pending_corporate_return_charges')->where('id',$pending_corporate_return_charge->id)->update(['national_charges_class_1' => '100%', 'national_charges_class_2' => '100%', 'national_charges_class_3' => '100%']);
        }

        $corporate_return_charges = DB::table('corporate_return_charges')->get();
        foreach ($corporate_return_charges as $corporate_return_charge) {
            DB::table('corporate_return_charges')->where('id',$corporate_return_charge->id)->update(['national_charges_class_1' => '100%', 'national_charges_class_2' => '100%', 'national_charges_class_3' => '100%']);
        }

        $corporate_standard_return_charges = DB::table('corporate_standard_return_charges')->get();
        foreach ($corporate_standard_return_charges as $corporate_standard_return_charge) {
            DB::table('corporate_standard_return_charges')->where('id',$corporate_standard_return_charge->id)->update(['national_charges_class_1' => '100%', 'national_charges_class_2' => '100%', 'national_charges_class_3' => '100%']);
        }
    }
}
