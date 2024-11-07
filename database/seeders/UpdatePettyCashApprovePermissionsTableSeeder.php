<?php

use App\Http\Models\Admin\ModulePermission;
use Illuminate\Database\Seeder;

class UpdatePettyCashApprovePermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = ModulePermission::find(173);
        $permissions->name = 'Petty Cash - Finance Approve';
        $permissions->save();

        DB::table('module_permissions')->insert(array(
            array('id' => 190, 'name' => 'Petty Cash - Station Approve', 'module_id' => 8),
            array('id' => 191, 'name' => 'Petty Cash - Operation Approve', 'module_id' => 8),
        ));
    }
}
