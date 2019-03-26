<?php

use Illuminate\Database\Seeder;

class UpdatePettyCashPermissionForActionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 173, 'name' => 'Petty Cash Statement - Approve', 'module_id' => 8),
        ));
    }
}
