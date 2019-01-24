<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Http\Models\Shipper\User;

class UpdateUserBankInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        //
        $id = User::select('id')->where('name','Walk-In')->first();
        DB::table('user_bank_infos')->insert(array(
            array('user_id' => $id['id'], 'bank_name' => 29, 'bank_branch' => '9912', 'account_no' => '0102951143', 'account_title' => 'Trax Online Private Limited', 'payment_mode' => 'ibft', 'payment_cycle' => 'Daily', 'city_id' => '202', 'iban' => 'PK35MPBL9904177140121797')
        ));
    }
}
