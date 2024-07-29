<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticCNIssueToRider extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 963, 'name' => 'Logistic CN Issue to Rider - View', 'module_id' => 34 ),
            array('id' => 964, 'name' => 'Logistic CN Issue to Rider - Add', 'module_id' => 34 ),
            array('id' => 965, 'name' => 'Logistic CN Issue to Rider - Edit', 'module_id' => 34 ),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 773, 'screen_name' => 'Logistic CN Issue to Rider', 'action' => 'View'),
            array('id' => 774, 'screen_name' => 'Logistic CN Issue to Rider', 'action' => 'Excel'),

        ));
    }
}
