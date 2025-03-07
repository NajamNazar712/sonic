<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TraxWebsiteNewLeadAccountEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->where('name', 'Trax Webite New Lead Account Email')->delete();

        DB::table('notifications')->insert(array(
            array(
                'id' => 230,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Trax Webite New Lead Account Email',
                'type_id' => 1,
                'subject' => 'Welcome to Trax [Company Name]',
                'body' => 'Dear [Full Name],' . PHP_EOL .
                PHP_EOL .
                'Welcome to signing up at TRAX for your company [Company Name]. Please verify your email address by clicking on the button/link below.' . PHP_EOL .
                PHP_EOL .
                '[Link]',
                
                'updated_by' => 615,
                'status' => 1,
            )
        ));
    }
}
