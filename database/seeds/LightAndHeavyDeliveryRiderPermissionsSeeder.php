<?php

use Illuminate\Database\Seeder;

class LightAndHeavyDeliveryRiderPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert(array(
            array('id' => 757, 'name' => 'Rider Category ByPass Request - View', 'module_id' => 6),
            array('id' => 758, 'name' => 'Rider Category ByPass Weight - View', 'module_id' => 6),
            array('id' => 759, 'name' => 'Rider Category ByPass Request (Add Request) - Action', 'module_id' => 6),
            array('id' => 760, 'name' => 'Rider Category ByPass Request (Approved) - Button', 'module_id' => 6),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 552, 'screen_name' => 'Vendor Invoice', 'action'=> 'View'),
            array('id' => 553, 'screen_name' => 'Vendors Accounts List', 'action'=> 'View'),
            array('id' => 554, 'screen_name' => 'Vendors Accounts List', 'action'=> 'View'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > Rider Category ByPass Request', 'url'=>'admin.delivery.note.rider_category_bypass_request', 'permission_id' => 757),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > Rider Category ByPass Weight', 'url'=>'admin.delivery.note.rider_category_bypass_weight', 'permission_id' => 758),
        ));
    }
}
