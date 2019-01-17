<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UpdateUsersTableWalkIn extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        //
        DB::table('users')->insert(array(
            array('name' => 'Walk-In', 'email' => 'Walk.in@trax.pk', 'password' => bcrypt('123456'), 'address' => 'Plot #4, DMCHS, Block #7/8, Adjacent to IBL Building Centre, Tipu Sultan Road, Karachi.','poc' => 'Trax', 'Phone' => '0304-1111232', 'cnic' => '00000-0000000-0', 'ntn_no' => '0000000_0', 'city_id' => '202', 'status' => '3', 'blacklist' => '0','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
