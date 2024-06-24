<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticChildCNReceiveAdminStore extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 966, 'name' => 'Logistic Child CN Receive Admin Store - View', 'module_id' => 34 ),
            array('id' => 967, 'name' => 'Logistic Child CN Receive Admin Store - Add', 'module_id' => 34 ),
            array('id' => 968, 'name' => 'Logistic Child CN Receive Admin Store - Edit', 'module_id' => 34 ),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 775, 'screen_name' => 'Logistic Child CN Receive Admin Store', 'action' => 'View'),
            array('id' => 776, 'screen_name' => 'Logistic Child CN Receive Admin Store', 'action' => 'Excel'),

        ));
    }
}
