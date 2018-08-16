<?php

use Illuminate\Database\Seeder;

class AdminRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_roles')->truncate();

        DB::table('admin_roles')->insert(array(
            array('id' => 1, 'name' => 'Super Administrator', 'department_id' => 1, 'updated_by' => 1)
        ));
    }
}
