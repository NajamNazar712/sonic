<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsForWeightFactorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 229, 'name' => 'Weight Charges Factor - View ', 'module_id' => 14),
        ));

    }
}
