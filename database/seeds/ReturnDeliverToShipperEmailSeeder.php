<?php

use App\Http\Models\Notification;
use Illuminate\Database\Seeder;

class ReturnDeliverToShipperEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        Notification::where('id',39)->update(
            ['updated_at' => $timestamp ,'body' => 'Dear [company_name], Hope you are doing great please note that below are returned details which back to you in safe and sound condition: [return_detail]'.PHP_EOL .'In case of any query regarding these shipments you may respond us back in 48 hours.']
        );
    }
}
