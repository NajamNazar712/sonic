<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableSettingsTickerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 152, 'name' => 'Ticker', 'module_id' => 14)
        ));
    }
}
