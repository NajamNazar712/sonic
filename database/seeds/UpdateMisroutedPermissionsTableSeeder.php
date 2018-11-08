<?php

use Illuminate\Database\Seeder;

class UpdateMisroutedPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 114, 'name' => 'Pending - View Rates', 'module_id' => 2),
            array('id' => 115, 'name' => 'Active - View Rates', 'module_id' => 2),
        ));
    }
}
