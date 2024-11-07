<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VisionSoftApiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vision_soft_apis')->truncate();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('vision_soft_apis')->insert(array(
            array('id' => 1, 'name' => 'Login', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Customers','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'CustomerBanks','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'CityHub','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'ArrivalRevenue','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 6, 'name' => 'CodPayable','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 7, 'name' => 'Employes','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 8, 'name' => 'DellRetRevenue','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 9, 'name' => 'PRCLoadAPIData','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 10, 'name' => 'CodPayment','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 11, 'name' => 'CodPaymentClear','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 12, 'name' => 'Cities','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 13, 'name' => 'BankDeposit','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 14, 'name' => 'DailyExp','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 15, 'name' => 'CodReceivable','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 16, 'name' => 'Adjustments','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
