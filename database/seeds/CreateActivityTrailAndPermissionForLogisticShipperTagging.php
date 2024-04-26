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
            array('id' => 979, 'name' => 'Logistic Booking - View', 'module_id' => 34 ),
            array('id' => 980, 'name' => 'Logistic Booking - Edit', 'module_id' => 34 ),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 783, 'screen_name' => 'Logistic Booking', 'action' => 'View'),
            array('id' => 784, 'screen_name' => 'Logistic Booking', 'action' => 'Excel'),
        ));
    }
}
