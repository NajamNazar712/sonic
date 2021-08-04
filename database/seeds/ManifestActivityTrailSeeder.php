<?php

use Illuminate\Database\Seeder;

class ManifestActivityTrailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 404, 'screen_name' => 'Cargo Manifest', 'action'=> 'View'),
            array('id' => 405, 'screen_name' => 'Cargo Manifest', 'action'=> 'Excel Download'),

        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 552, 'name' => 'Manifest - View', 'module_id' => 32),
        ));


        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Cargo Vehicle Manifest > Manifest', 'url'=>'admin.cargo_manifest.index', 'permission_id' => 552),

        ));

    }
}
