<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateActivityTrailAndPermissionForSackBagStatusReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 936, 'name' => 'Canvas Bag Status Report - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 743, 'screen_name' => 'Canvas Bag Status', 'action' => 'View'),
            array('id' => 744, 'screen_name' => 'Canvas Bag Status', 'action' => 'Excel'),

        ));


    }
}
