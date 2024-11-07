<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Http\Models\Admin\GlobalSettings;

class AddDeliveryRevertAccessToGlobalSetting extends Seeder
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
            'type' => 'delivery_revert_access',
            'text' => '',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
