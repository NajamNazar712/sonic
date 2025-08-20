<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class NewMessagesForBAHL extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $timestamp = \Carbon\Carbon::now();

        $timestamp = now(); // Or any valid timestamp

        DB::table('notifications')->insert(array(
            array(
                'id' => 246,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Returned of Debit Card - Bank Al Habib LHE & KHI (Cards)',
                'type_id' => 2,
                'subject' => null,
                'body' => 'Dear BAHL Customer,' . PHP_EOL . PHP_EOL .'Your Debit Card has been returned from given registered mailing address. Please collect Debit Card from your relationship branch.',
                'updated_by' => 3756,
                'status' => 1
            )
        ));

        DB::table('notifications')->insert(array(
            array(
                'id' => 247 ,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Delivered - Bank Al Habib LHE & KHI (Cards)',
                'type_id' => 2,
                'subject' => null,
                'body' => 'Dear BAHL Customer,' . PHP_EOL . PHP_EOL .'Your Debit Card has already been delivered at given registered mailing address. Please collect your Debit Card from your relationship branch.' ,
                'updated_by' => 3756,
                'status' => 1
            )
        ));
    }
}
