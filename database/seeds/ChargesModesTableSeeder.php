<?php

use Illuminate\Database\Seeder;

class ChargesModesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('charges_modes')->truncate();
        DB::table('charges_modes')->insert(array(
            array('id'=>1, 'charges_mode'=>'At Booking'),
            array('id'=>2, 'charges_mode'=>'2Pay')

        ));
    }
}
