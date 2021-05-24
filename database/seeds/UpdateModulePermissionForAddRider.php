<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAddRider extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 500, 'name' => 'Incentive Screen Add-Rider', 'module_id' => 12),
        ));
        DB::table('module_permissions')->where('id',93)->update(['name' => 'Permanent Screen Add-Rider']);
        
    }
}
