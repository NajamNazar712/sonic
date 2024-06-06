<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticProduct extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 955, 'name' => 'Logistic Product - View', 'module_id' => 34 ),
            array('id' => 956, 'name' => 'Logistic Product - Add', 'module_id' => 34 ),
            array('id' => 957, 'name' => 'Logistic Product - Edit', 'module_id' => 34 ),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 767, 'screen_name' => 'Logistic Product', 'action' => 'View'),
            array('id' => 768, 'screen_name' => 'Logistic Product', 'action' => 'Excel'),

        ));
    }
}
