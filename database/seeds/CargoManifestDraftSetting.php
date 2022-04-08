<?php

use Illuminate\Database\Seeder;
//use DB;

class CargoManifestDraftSetting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->where('id', 682)->update(['module_id' => 24]);
    }
}
