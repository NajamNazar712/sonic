<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCrmAutoCommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 325, 'name' => 'Auto Comment', 'module_id' => 14)
        ));
    }
}
