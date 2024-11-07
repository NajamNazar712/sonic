<?php

use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PackagingMaterialBookingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $id = User::select('id')->where('id', 1691)->first();
        DB::table('global_settings')->insert(array(
            array('type' => 'packaging_material_stock_movement_account_id', 'setting_value' => $id->id, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
