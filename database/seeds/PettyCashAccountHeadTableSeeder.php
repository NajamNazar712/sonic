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

        DB::table('petty_cash_account_head')->insert(array(
            array('id' =>1, 'name'=> 'Cargo Forwarding'),
            array('id' =>2, 'name'=> 'Communication/Internet'),
            array('id' =>3, 'name'=> 'Delivery Commission'),
            array('id' =>4, 'name'=> 'Entertainment'),
            array('id' =>5, 'name'=> 'Finance lease charges / Financial Charges'),
            array('id' =>6, 'name'=> 'Fuel'),
            array('id' =>7, 'name'=> 'Others'),
            array('id' =>8, 'name'=> 'Printing & Stationary'),
            array('id' =>9, 'name'=> 'Rent'),
            array('id' =>10, 'name'=> 'Repair & maintenance'),
            array('id' =>11, 'name'=> 'Sales Comission'),
            array('id' =>12, 'name'=> 'Server Hosting'),
            array('id' =>13, 'name'=> 'Software Support'),
            array('id' =>14, 'name'=> 'Staff Entertainment'),
            array('id' =>15, 'name'=> 'Support Salaries'),
            array('id' =>16, 'name'=> 'Travelling & conveyance'),
            array('id' =>17, 'name'=> 'Utilities'),
            array('id' =>18, 'name'=> 'Variable Salaries'),
        ));
    }
}
