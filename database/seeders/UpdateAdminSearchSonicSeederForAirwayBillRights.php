<?php

use Illuminate\Database\Seeder;

class UpdateAdminSearchSonicSeederForAirwayBillRights extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support  >  CN Print Rights Setting', 'url'=>'admin.settings.cn_print_right.cn_print_right', 'permission_id' => 699));
    }
}
