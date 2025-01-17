<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticService extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 958, 'name' => 'Logistic Service - View', 'module_id' => 34 ),
            array('id' => 959, 'name' => 'Logistic Service - Add', 'module_id' => 34 ),
            array('id' => 960, 'name' => 'Logistic Service - Edit', 'module_id' => 34 ),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 769, 'screen_name' => 'Logistic Service', 'action' => 'View'),
            array('id' => 770, 'screen_name' => 'Logistic Service', 'action' => 'Excel'),

        ));
    }
}
