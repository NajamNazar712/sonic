<?php

use Illuminate\Database\Seeder;

class DisputeTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dispute_types')->truncate();
        DB::table('dispute_types')->insert(array(
            array('id'=>1,'type'=>'Pickup - W/O Receiving Sheet'),
            array('id'=>2,'type'=>'Pickup - Short Receiving'),
            array('id'=>3,'type'=>'Damaged'),
            array('id'=>4,'type'=>'Lost'),
            array('id'=>5,'type'=>'Data change of Consignee'),
            array('id'=>6,'type'=>'Misroute'),
            array('id'=>7,'type'=>'Cargo - Short Receiving'),
            array('id'=>8,'type'=>'Different Status'),
            array('id'=>9,'type'=>'Delay'),
            array('id'=>10,'type'=>'Junction - Cargo  Not Updated'),


        ));
    }
}
