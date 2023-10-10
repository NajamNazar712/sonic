<?php

use Illuminate\Database\Seeder;

class UpdateSarNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notifications')
            ->where('id', 220)
            ->update(['subject' => 'Undelivered Shipment Details | [date] ']);
    }
}
