<?php

use Illuminate\Database\Seeder;

class DeliveryTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('delivery_types')->truncate();
        DB::table('delivery_types')->insert(array(
            array('id'=>1,'delivery_type'=>'Doorstep'),
            array('id'=>2,'delivery_type'=>'Hub to Hub'),
        ));
    }
}
