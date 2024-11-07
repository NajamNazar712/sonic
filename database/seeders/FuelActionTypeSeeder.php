<?php

use Illuminate\Database\Seeder;

class FuelActionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('action_types')->truncate();
        DB::table('action_types')->insert(array(
            array('id' => 1 ,'name'=>'New'),
            array('id' => 2 ,'name'=>'Reassign'),
            array('id' => 3 ,'name'=>'Block'),
            array('id' => 4 ,'name'=>'Unblock'),
            array('id' => 5 ,'name'=>'Edit'),
            array('id' => 6 ,'name'=>'Approve'),
        ));
    }
}
