<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDeleteReturnNoteImage extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 354, 'name' => 'Delete Image - Return Note', 'module_id' => 7)
        ));
    }
}
