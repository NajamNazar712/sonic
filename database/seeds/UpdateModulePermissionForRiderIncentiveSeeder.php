<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRiderIncentiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 491, 'name' => 'Rider Incentive Settings', 'module_id' => 14),
            array('id' => 492, 'name' => 'Rider Incentive - View', 'module_id' => 28)
        ));
    }
}
