<?php

use Illuminate\Database\Seeder;

class PettyCashAccountHeadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('petty_cash_account_heads')->truncate();
        $time = \Carbon\Carbon::now();
        DB::table('petty_cash_account_heads')->insert(array(
            array('id' =>1, 'name'=> 'Cargo Forwarding', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>2, 'name'=> 'Communication/Internet', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>3, 'name'=> 'Delivery Commission', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>4, 'name'=> 'Entertainment', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>5, 'name'=> 'Finance lease charges / Financial Charges', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>6, 'name'=> 'Fuel', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>7, 'name'=> 'Others', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>8, 'name'=> 'Printing & Stationary', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>9, 'name'=> 'Rent', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>10, 'name'=> 'Repair & maintenance', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>11, 'name'=> 'Sales Comission', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>12, 'name'=> 'Server Hosting', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>13, 'name'=> 'Software Support', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>14, 'name'=> 'Staff Entertainment', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>15, 'name'=> 'Support Salaries', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>16, 'name'=> 'Travelling & conveyance', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>17, 'name'=> 'Utilities', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>18, 'name'=> 'Variable Salaries', 'created_at' => $time, 'updated_at' => $time),
        ));
    }
}
