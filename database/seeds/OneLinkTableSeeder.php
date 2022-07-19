<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OneLinkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('one_links')->truncate();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('one_links')->insert(array(
            array('id'=>1,'username'=>"Trax8729",'password'=>Hash::make("Dyz4xABEsrE9XeLj"),'bank_mnemonic' => 'TRAX0001' , 'consumer_prefix' => 100097, 'remember_token' => "Dyz4xABEsrE9XeLj" ,'active'=>0,'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
