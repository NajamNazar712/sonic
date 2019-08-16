<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPettyDraftTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 238, 'name' => 'Petty Cash Statement Draft - View', 'module_id' => 9),
            array('id' => 239, 'name' => 'Petty Cash Statement Draft - Edit', 'module_id' => 9),
        ));
    }
}
