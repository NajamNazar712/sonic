<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateAdminScreenListForLogiticCNReceiveAdminStore extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Logistic > CN Receive Admin Store', 'url' => 'admin.logistic.cn.receive_admin_store.index', 'permission_id' => 972),
        ));

    }
}
