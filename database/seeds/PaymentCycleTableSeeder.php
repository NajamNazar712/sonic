<?php

use Illuminate\Database\Seeder;

class PaymentCycleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();
        DB::table('payment_cycles')->truncate();

        DB::table('payment_cycles')->insert(array(
            array('id' => 1 ,'name'=>'Daily', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2 , 'name'=>'Weekly', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3 ,'name'=>'Monthly', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
