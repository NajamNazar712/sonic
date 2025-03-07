<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticBatch extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 976, 'name' => 'Logistic Batch - View', 'module_id' => 34 ),
//            array('id' => 974, 'name' => 'Logistic Shipper Tagging - Add', 'module_id' => 34 ),
            array('id' => 977, 'name' => 'Select Logistic Batch - Button', 'module_id' => 34 ),
            array('id' => 985, 'name' => 'Logistic Batch Bookings', 'module_id' => 34 ),

        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 781, 'screen_name' => 'Logistic Batch', 'action' => 'View'),
            array('id' => 782, 'screen_name' => 'Logistic Batch', 'action' => 'Excel'),
            array('id' => 793, 'screen_name' => 'Logistic Batch Bookings', 'action' => 'View'),
            array('id' => 794, 'screen_name' => 'Logistic Batch Bookings', 'action' => 'Excel'),
        ));
    }
}
