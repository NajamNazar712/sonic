<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForInternationalUserCreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 590, 'name' => 'International Credit Limit', 'module_id' => 2),
        ));
    }
}
