<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Shipper\User;

use Carbon\Carbon;

class UpdateGlobalSettingsTableWalkInCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $id = User::select('id')->where('name','Walk-In')->first();
        DB::table('global_settings')->insert(array(
            array('type' => 'Walk-In', 'setting_value' => $id->id, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
