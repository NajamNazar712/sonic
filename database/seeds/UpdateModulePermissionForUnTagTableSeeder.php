<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForUnTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->where('module_id',309)->delete();
        
        DB::table('module_permissions')->insert(array(
            array('id' => 309, 'name' => 'Tag / Un Tag', 'module_id' => 18),
        ));
    }
}
