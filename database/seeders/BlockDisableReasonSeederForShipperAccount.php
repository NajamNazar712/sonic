<?php

use Carbon\Carbon;
use App\BlockDisableReasonUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockDisableReasonSeederForShipperAccount extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('block_disable_reason_users')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('block_disable_reason_users')->insert(array(
            array('id' => 1, 'name' => 'Service Issues', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Payment/Claim Issues', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Switched due to Rates', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'Business Permanent Discontinued', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'Customer Internal Issues', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 6, 'name' => 'Sales Person Dealing Issue', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
           
    }
}
