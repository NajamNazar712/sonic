<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateActivityTrailAndPermissionForSackBag extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 934, 'name' => 'Canvas Bag - View', 'module_id' => 32),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 739, 'screen_name' => 'Canvas Bag', 'action' => 'View'),
            array('id' => 740, 'screen_name' => 'Canvas Bag', 'action' => 'Excel'),

        ));


    }
}
