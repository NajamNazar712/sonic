<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class WalletApiNatures extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('wallet_api_natures')->insert([
            ['id' => 1 , 'name' => 'on-boarding-request' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 2 , 'name' => 'on-boarding-response' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 3 , 'name' => 'log-request' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 4 , 'name' => 'log-response' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 5 , 'name' => 'log-charge-request' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 6 , 'name' => 'log-charge-response' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 7 , 'name' => 'settlement-request' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 8 , 'name' => 'settlement-response' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 9 , 'name' => 'adjustment-request' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 10 , 'name' => 'adjustment-response' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 11 , 'name' => 'shipment-status-request' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 12 , 'name' => 'shipment-status-response' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 13 , 'name' => 'amount-change-request' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 14 , 'name' => 'amount-change-response' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],

            ['id' => 15 , 'name' => 'bulk-shipment-status-request' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],
            ['id' => 16 , 'name' => 'bulk-shipment-status-response' ,'created_at' => $timestamp, 'updated_at' =>$timestamp ],


        ]);
    }
}
