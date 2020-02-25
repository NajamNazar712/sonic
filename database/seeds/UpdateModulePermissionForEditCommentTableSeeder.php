<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForEditCommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 310, 'name' => 'Edit Comment', 'module_id' => 18)
        ));
    }
}
