<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticChildCNIssueToRider extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 969, 'name' => 'Logistic Child CN Issue to Rider - View', 'module_id' => 34 ),
            array('id' => 970, 'name' => 'Logistic Child CN Issue to Rider - Add', 'module_id' => 34 ),
            array('id' => 971, 'name' => 'Logistic Child CN Issue to Rider - Edit', 'module_id' => 34 ),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 777, 'screen_name' => 'Logistic Child CN Issue to Rider', 'action' => 'View'),
            array('id' => 778, 'screen_name' => 'Logistic Child CN Issue to Rider', 'action' => 'Excel'),

        ));
    }
}
