<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSisterAccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 241, 'name' => 'Sister Account - Add', 'module_id' => 2),
            array('id' => 242, 'name' => 'Sister Account - Edit/Mapping', 'module_id' => 2),
        ));
    }
}
