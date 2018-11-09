<?php

use Illuminate\Database\Seeder;

class UpdateMisroutedPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 119, 'name' => 'Misrouted History - View', 'module_id' => 6),
        ));
    }
}
