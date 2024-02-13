<?php

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
            array('id' => 934, 'name' => 'Sack Bag - View', 'module_id' => 32),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 739, 'screen_name' => 'Sack Bag', 'action' => 'View'),
            array('id' => 740, 'screen_name' => 'Sack Bag', 'action' => 'Excel'),

        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Cargo Vehicle Manifest > Bag > Sack Bag', 'url' => 'admin.cargo_manifest.bags.sack_bag.index', 'permission_id' => 934),
        ));
    }
}
