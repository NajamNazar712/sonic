<?php

use Illuminate\Database\Seeder;

class Seeder5166ForPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 720, 'name' => 'Rider OTP', 'module_id' => 12),
            array('id' => 721, 'name' => 'Rider OTP Toggle', 'module_id' => 12),
            array('id' => 722, 'name' => 'Admin OTP Toggle', 'module_id' => 11),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Rider OTP', 'url'=>'admin.rider_otp.index', 'permission_id' => 720)
        );
    }
}
