<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForLogisticBooking2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 1002, 'name' => 'Logistic Book - Excel', 'module_id' => 34 ),
        ));
    }
}
