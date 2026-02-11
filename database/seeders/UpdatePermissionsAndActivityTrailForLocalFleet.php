<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatePermissionsAndActivityTrailForLocalFleet extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 1062, 'name' => 'Local Fleet View Vehicle', 'module_id' => 24),
            array('id' => 1063, 'name' => 'Local Fleet Add Vehicle', 'module_id' => 24),
            array('id' => 1064, 'name' => 'Local Fleet Add Vehicle Document', 'module_id' => 24),
            array('id' => 1065, 'name' => 'Local Fleet View Vehicle Document', 'module_id' => 24),
            array('id' => 1066, 'name' => 'Local Fleet Update Vehicle', 'module_id' => 24),
            array('id' => 1067, 'name' => 'Local Fleet Generate Vehicle Barcode', 'module_id' => 24),
            array('id' => 1068, 'name' => 'Local Fleet view Vehicle Trips', 'module_id' => 24),
            array('id' => 1069, 'name' => 'Local Fleet View Daily Activity Report', 'module_id' => 24),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 840, 'screen_name' => 'Local Fleet View Vehicle', 'action'=> 'View'),
            array('id' => 841, 'screen_name' => 'Local Fleet view Vehicle Trips', 'action'=> 'View'),
            array('id' => 842, 'screen_name' => 'Local Fleet View Daily Activity Report', 'action'=> 'View'),
        ));

//        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
//        DB::table('admins_screen_list')->insert(array(
//            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Logistic > Setup > Master Product', 'url' => 'admin.logistic.master_product.index', 'permission_id' => 952),
//        ));
    }
}
