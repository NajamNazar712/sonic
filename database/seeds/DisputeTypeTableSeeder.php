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
            array('id'=>1,'type'=>'Short Receiving'),
            array('id'=>2,'type'=>'Misroute')


        ));
    }
}
