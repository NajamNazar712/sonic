<?php

use Illuminate\Database\Seeder;

class PickupTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pickup_types')->truncate();
        DB::table('pickup_types')->insert(array(
            array('pickup'=>'Single Address'),
            array('pickup'=>'Multiple Addresses'),
        ));
    }
}
