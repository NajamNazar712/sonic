<?php

use Illuminate\Database\Seeder;

class InsidenceCommentPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 581, 'name' => 'Incidence Comment', 'module_id' => 31),
        ));
    }
}
