<?php

use Illuminate\Database\Seeder;

class UpdatePickupsPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 113, 'name' => 'Booked VS Received - View', 'module_id' => 3),
        ));
    }
}
