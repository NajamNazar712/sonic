<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticShipperTagging extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 973, 'name' => 'Logistic Shipper Tagging - View', 'module_id' => 34 ),
            array('id' => 974, 'name' => 'Logistic Shipper Tagging - Add', 'module_id' => 34 ),
            array('id' => 975, 'name' => 'Logistic Shipper Tagging - Edit', 'module_id' => 34 ),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 779, 'screen_name' => 'Logistic Shipper Tagging', 'action' => 'View'),
            array('id' => 780, 'screen_name' => 'Logistic Shipper Tagging', 'action' => 'Excel'),
        ));
    }
}
