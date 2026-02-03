<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class PermissionForRiderLogs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('module_permissions')->insert(array(
            array('id' => 1060, 'name' => 'Rider Management - Logs', 'module_id' => 12),
        ));
    }
}
