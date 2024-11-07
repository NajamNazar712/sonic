<?php

use Illuminate\Database\Seeder;

class PermissionsForRetailDiscountCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 801, 'screen_name' => 'Retail Discount Codes', 'action'=> 'View'),
            array('id' => 802, 'screen_name' => 'Retail Discount Codes', 'action'=> 'Excel'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Retail > Retail Discount Codes', 'url'=>'admin.retail.retail_discount_codes.index', 'permission_id' => 1003)
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 1003, 'name' => 'Retail Discount Codes - View', 'module_id' => 14),
        ));
    }
}
