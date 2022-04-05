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
        $data = DB::table('module_permissions')->insert([
            'id' => '682',
            'name' => 'Cargo Manifest',
            'module_id' => '24',
        ]);
    }
}
