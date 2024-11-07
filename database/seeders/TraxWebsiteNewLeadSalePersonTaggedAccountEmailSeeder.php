<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TraxWebsiteNewLeadSalePersonTaggedAccountEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->where('name', 'Trax Webite New Lead Sale Person Tagged Account Email')->delete();

        DB::table('notifications')->insert(array(
            array(
                'id' => 231,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Trax Webite New Lead Sale Person Tagged Account Email',
                'type_id' => 1,
                'subject' => 'Custom Quotes For [Company Name]',
                'body' => 'Dear salesperson,' . PHP_EOL .
                          'Your tagged account [Company Name], [account ID] has requested custom quotes. Please check in portal',
                'updated_by' => 615,
                'status' => 1,
            )
        ));
        
    }
}
