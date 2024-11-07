<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForBookedAndCancelledTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 259, 'name' => 'Booked And Cancelled', 'module_id' => 9),
        ));
    }
}
