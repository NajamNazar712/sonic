<?php

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Database\Seeder;

class CreateBotSMSUserForSupervisorDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::create([
            'name' => 'BOT SMS',
            'email' => 'bot_sms@trax.pk',
            'phone_number' => '09007860121',
            'cnic' => '33333-3333333-3',
            'role_id' => 1,
            'password' => '$2y$10$XW1bVUtw5BhyvMU/rEtit.6yugg5jewx1q3Z/RQMo5xFOxaAJrlkq',
            'status' => 1
        ]);

        GlobalSettings::create([
            'setting_value' =>  $admin->id,
            'type' => 'bot_sms_id',
            'text' => 'For BOT SMS Debriefing Supervisor'

        ]);
    }
}
