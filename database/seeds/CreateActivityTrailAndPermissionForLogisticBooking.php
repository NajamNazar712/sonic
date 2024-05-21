<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticBooking extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 986, 'name' => 'Logistic Book - View', 'module_id' => 34 ),
            array('id' => 987, 'name' => 'Logistic Book - Edit', 'module_id' => 34 ),
            array('id' => 988, 'name' => 'Logistic Book - Add', 'module_id' => 34 ),

        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 791, 'screen_name' => 'Logistic Book', 'action' => 'View'),
            array('id' => 792, 'screen_name' => 'Logistic Book', 'action' => 'Excel'),

        ));
    }
}
