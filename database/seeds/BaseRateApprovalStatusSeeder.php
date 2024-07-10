<?php

use Illuminate\Database\Seeder;

class BaseRateApprovalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('base_rate_revision_approval_statuses')->truncate();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('base_rate_revision_approval_statuses')->insert(array(
            array('id' => 1, 'name' => 'Pending', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Approved','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Rejected', 'created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
