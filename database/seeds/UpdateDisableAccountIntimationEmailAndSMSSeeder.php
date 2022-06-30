<?php

use Illuminate\Database\Seeder;

class UpdateDisableAccountIntimationEmailAndSMSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->insert(array(
            array('id' => 179 , 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Disable Account Intimation on Email ', 'type_id' => 1, 'subject' => 'Trax Survey', 'body' => 'Dear Shipper, Please fill this survey form [link]', 'updated_by' => 7, 'status' => 1),
            array('id' => 180 ,'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Disable Account Intimation on SMS ', 'type_id' => 2, 'subject' => null, 'body' => 'Dear Shipper, Please fill this survey form [link]', 'updated_by' => 7, 'status' => 1)
        ));
    }
}
