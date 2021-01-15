<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Shipper\User;

class UpdateUsersFroRetailUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('users')->insert(array(
            array('name' => 'Retail Store - Trax', 'email' => 'retail@trax.pk', 'password' => bcrypt('123456'), 'address' => 'Plot #105, Sector 7A, Mehran Town, Korangi, Karachi.','poc' => 'Trax', 'Phone' => '0304-1111232', 'cnic' => '00000-0000000-0', 'ntn_no' => '0000000_0', 'city_id' => '202', 'status' => 3, 'blacklist' => '0','created_at'=>$timestamp,'updated_at'=>$timestamp, 'activated_at' => $timestamp, 'product_id' => 22)
        ));
        $user = User::select('id')->where('name','Retail Store - Trax')->first();
        DB::table('user_bank_infos')->insert(array(
            array('user_id' => $user->id, 'bank_name' => 29, 'bank_branch' => '9912', 'account_no' => '0102951143', 'account_title' => 'Trax Online Private Limited', 'city_id' => '202', 'iban' => 'PK35MPBL9904177140121797')
        ));
        DB::table('global_settings')->insert(array(
            array('type' => 'retail_store', 'setting_value' => $user->id, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
