<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadProgressSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lead_progess_settings')->insert([
            [
                'stage' => 'Signed up',
                'trigger' => 'When a user signs up after adding email and password from lead form email',
                'percent' => 30,
                'color' => '#FF4961'
            ],
            [
                'stage' => 'Custom Rates Requested',
                'trigger' => 'When a user requests a custom quotation on CRF submission',
                'percent' => 50,
                'color' => '#FF9149'
            ],
            [
                'stage' => 'Registration Confirmed',
                'trigger' => 'When a user submits the CRF at default rates / rates are approved',
                'percent' => 80,
                'color' => '#1E9FF2'
            ],
            [
                'stage' => 'Documents Verified',
                'trigger' => 'When documents are approved & account stage is pending for activation by the admin',
                'percent' => 95,
                'color' => '#FFD700'
            ],
            [
                'stage' => 'Account Activated',
                'trigger' => 'Account is activated',
                'percent' => 100,
                'color' => '#00D082'
            ]
        ]);

    }
}
