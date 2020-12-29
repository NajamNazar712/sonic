<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForForwardAndUpdateLeadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 419, 'name' => 'Lead - Update Status', 'module_id' => 25),
            array('id' => 420, 'name' => 'Lead - Forward Lead', 'module_id' => 25),
        ));
    }
}
