<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InProcessResolveButtonPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permission for bulk resolve button
        DB::table('module_permissions')->insert(array(
            array('id' => 1013, 'name' => 'In-Process - Resolve Button', 'module_id' => 18),
        ));
    }
}
