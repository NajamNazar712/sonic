<?php

use Illuminate\Database\Seeder;

class UpdateModuleIdForSomePermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->whereIn('id',[98,99,381,382])->update([
            'module_id' => 11,
        ]);
    }
}
