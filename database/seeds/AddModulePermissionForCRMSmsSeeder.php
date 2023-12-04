<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\ModulePermission;

class AddModulePermissionForCRMSmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModulePermission::where('id', 914)->delete();
        
        DB::table('module_permissions')->insert(array(
            array('id' => 914, 'name' => 'CRM SMS - View', 'module_id' => 18),
        
        ));
    }
}
