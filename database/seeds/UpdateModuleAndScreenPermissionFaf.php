<?php

use Illuminate\Database\Seeder;

class UpdateModuleAndScreenPermissionFaf extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 997, 'name' => 'Fuel Factor', 'module_id' => 14),
            array('id' => 998, 'name' => 'Fuel Factor Apply', 'module_id' => 14),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Financials  >  FAF Charges', 'url'=>'admin.settings.faf_charges.index', 'permission_id' => 997)
        ));
    }
}
