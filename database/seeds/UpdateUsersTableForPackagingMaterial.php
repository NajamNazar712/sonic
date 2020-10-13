<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Http\Models\Shipper\User;

class UpdateUsersTableForPackagingMaterial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('users')->insert(array(
            array('name' => 'Packaging Material - Trax', 'email' => 'packaging_material@trax.pk', 'password' => bcrypt('123456'), 'address' => 'Plot #105, Sector 7A, Mehran Town, Korangi, Karachi.','poc' => 'Trax', 'Phone' => '0304-1111232', 'cnic' => '00000-0000000-0', 'ntn_no' => '0000000_0', 'city_id' => '202', 'status' => 3, 'blacklist' => '0','created_at'=>$timestamp,'updated_at'=>$timestamp, 'activated_at' => $timestamp, 'product_id' => 22)
        ));
        $user = User::select('id')->where('name','Packaging Material - Trax')->first();
        DB::table('user_bank_infos')->insert(array(
            array('user_id' => $user->id, 'bank_name' => 29, 'bank_branch' => '9912', 'account_no' => '0102951143', 'account_title' => 'Trax Online Private Limited', 'city_id' => '202', 'iban' => 'PK35MPBL9904177140121797')
        ));
        DB::table('global_settings')->insert(array(
            array('type' => 'packaging_material', 'setting_value' => $user->id, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
        DB::table('wms_user_informations')->insert(array(
            array('user_id' => $user->id, 'warehousing' => 1, 'invoicing_cycle' => 1, 'invoicing_date' => 1, 'per_product_charges' => 1, 'per_square_foot_charges' => 1, 'packing_charges' => 1, 'labelling_charges' => 1, 'storage_charges' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
//        DB::table('user_shipping_infos')->insert(array(
//            array('user_id' => $user->id, 'pickup_address' => 'Trax Warehouse Karachi', 'poc' => 'Trax Warehouse Karachi', 'phone' => '0304-1111232', 'email' => 'packaging_material@trax.pk', 'city_id' => '202', 'hidden' => 0, 'default_address' => 0, 'status' => 1, 'warehouse' => 1,'created_at'=>$timestamp,'updated_at'=>$timestamp)
//        ));
    }
}
