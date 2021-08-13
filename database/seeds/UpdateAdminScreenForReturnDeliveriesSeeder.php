<?php

use Illuminate\Database\Seeder;

class UpdateAdminScreenForReturnDeliveriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile  >  Return  >  Return Deliveries', 'url'=>'admin.return.return_deliveries.index', 'permission_id' => 566));
    }
}
