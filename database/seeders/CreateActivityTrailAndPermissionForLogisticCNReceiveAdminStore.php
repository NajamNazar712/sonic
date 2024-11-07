<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticCNReceiveAdminStore extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 972, 'name' => 'Logistic CN Receive Admin Store - View', 'module_id' => 34 ),
            array('id' => 961, 'name' => 'Logistic CN Receive Admin Store - Add', 'module_id' => 34 ),
            array('id' => 962, 'name' => 'Logistic CN Receive Admin Store - Edit', 'module_id' => 34 ),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 771, 'screen_name' => 'Logistic CN Receive Admin Store', 'action' => 'View'),
            array('id' => 772, 'screen_name' => 'Logistic CN Receive Admin Store', 'action' => 'Excel'),

        ));

    }
}
