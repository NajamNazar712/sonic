<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForParcelHistoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 392, 'name' => 'Open Parcel History', 'module_id' => 14),
        ));
    }
}
