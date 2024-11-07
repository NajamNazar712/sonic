<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateActivityTrailAndPermissionForUtilizationofSackBag extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 935, 'name' => 'Utilization of Canvas Bag Report - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 741, 'screen_name' => 'Utilization of Canvas Bag', 'action' => 'View'),
            array('id' => 742, 'screen_name' => 'Utilization of Canvas Bag', 'action' => 'Excel'),

        ));

    }
}
