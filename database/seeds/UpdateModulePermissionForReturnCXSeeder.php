<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReturnCXSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 266, 'name' => 'Return for CX and Sales - View', 'module_id' => 7),
        ));
    }
}
