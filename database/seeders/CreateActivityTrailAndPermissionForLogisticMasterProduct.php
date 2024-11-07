<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticMasterProduct extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 952, 'name' => 'Logistic Master Product - View', 'module_id' => 34 ),
            array('id' => 953, 'name' => 'Logistic Master Product - Add', 'module_id' => 34 ),
            array('id' => 954, 'name' => 'Logistic Master Product - Edit', 'module_id' => 34 ),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 765, 'screen_name' => 'Logistic Master Product', 'action' => 'View'),
            array('id' => 766, 'screen_name' => 'Logistic Master Product', 'action' => 'Excel'),

        ));

    }
}
