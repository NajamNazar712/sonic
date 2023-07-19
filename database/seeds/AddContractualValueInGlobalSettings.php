<?php

use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Database\Seeder;

class AddContractualValueInGlobalSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GlobalSettings::insert([
            'setting_value' => 0,
            'type' => 'latest_contractual_id',
            'text' => 'For Creation of Contractual Employees',
        ]);
    }
}
