<?php

use Illuminate\Database\Seeder;

class PendingDeliveriesReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->where('id', 110)->delete();
        DB::table('notifications')->insert(array(
            array('id' => 110, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pending Deliveries Report Email', 'type_id' => 1, 'subject' => '[date] Pending Deliveries', 'body' => 'Please check daily report dated [date],' . PHP_EOL . '[link]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
