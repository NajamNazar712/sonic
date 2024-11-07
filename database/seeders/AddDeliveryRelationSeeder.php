<?php

use Illuminate\Database\Seeder;

class AddDeliveryRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('delivery_relations')->insert(array(
            array('id' => 1 ,'name'=>'Self'),
            array('id' => 2 ,'name'=>'Mother'),
            array('id' => 3 ,'name'=>'Father'),
            array('id' => 4 ,'name'=>'Sister'),
            array('id' => 5 ,'name'=>'Brother'),
            array('id' => 6 ,'name'=>'Wife'),
        ));
    }
}
