<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPettyCashCheckTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 473, 'name' => 'Petty Cash Statement - Check', 'module_id' => 8),
        ));
    }
}
