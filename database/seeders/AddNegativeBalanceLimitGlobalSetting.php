<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\GlobalSettings;

class AddNegativeBalanceLimitGlobalSetting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GlobalSettings::insert([
            'setting_value' => -1000,
            'type' => 'negative_payable_limit',
            'text' => 'For Negative Balance Booking Restriction',
        ]);
    }
}
