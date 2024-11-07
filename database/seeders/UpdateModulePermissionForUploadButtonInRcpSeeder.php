<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateModulePermissionForUploadButtonInRcpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 884, 'name' => 'Return Confirmation Pending - Upload Button', 'module_id' => 7)
        ));
    }
}
