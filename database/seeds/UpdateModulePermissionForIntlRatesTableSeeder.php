<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForIntlRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 439, 'name' => 'International Rates - Add/Edit', 'module_id' => 2),
            array('id' => 440, 'name' => 'International Rates - View', 'module_id' => 2)
        ));
    }
}
