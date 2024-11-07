<?php

use Illuminate\Database\Seeder;

class AddRCPTATOptions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rcp_tat_options')->truncate();

        DB::table('rcp_tat_options')->insert(array(
            array('id' => 1, 'name' => 'As per shipper','value' => 0),
            array('id' => 2, 'name' => 'Next day','value' => 1),
            array('id' => 3, 'name' => '2 days','value' => 2),
            array('id' => 4, 'name' => '3 days','value' => 3),
            array('id' => 5, 'name' => '4 days','value' => 4),
            array('id' => 6, 'name' => '5 days','value' => 5),
            array('id' => 7, 'name' => '6 days','value' => 6),
            array('id' => 8, 'name' => '7 days','value' => 7),
        ));
    }
}
