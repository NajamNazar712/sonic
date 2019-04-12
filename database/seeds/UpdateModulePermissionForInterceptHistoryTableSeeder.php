<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForInterceptHistoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 196, 'name' => 'Intercept Rebook History - View', 'module_id' => 6),
        ));
    }
}
