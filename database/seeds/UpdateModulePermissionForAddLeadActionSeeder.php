<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class UpdateModulePermissionForAddLeadActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lead_references')->insert(array(
           array('id' => 7, 'name' => 'Sonic', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now())
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 769, 'name' => 'Lead Add - Action', 'module_id' => 25),
        ));
    }
}
