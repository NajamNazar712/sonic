<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForTelenorCallTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 402, 'name' => 'Telenor Calls', 'module_id' => 6),
        ));
    }
}
