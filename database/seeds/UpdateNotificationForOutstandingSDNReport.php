<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForOutstandingSDNReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 90, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Outstanding SDN Report', 'type_id' => 1, 'subject' => 'Outstanding SDN Report', 'body' => 'Outstanding SDN Report'. PHP_EOL . '[link]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
