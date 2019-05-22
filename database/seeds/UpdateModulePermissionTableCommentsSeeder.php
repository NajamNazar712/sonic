<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableCommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 201, 'name' => 'Comment', 'module_id' => 18)
        ));
    }
}
