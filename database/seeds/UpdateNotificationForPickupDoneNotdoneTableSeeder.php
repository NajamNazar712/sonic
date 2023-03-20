<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForPickupDoneNotdoneTableSeeder extends Seeder
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
            array('id' => 50, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pickup Request Done/Not Done', 'type_id' => 1, 'subject' => "Pickup Request [status]", 'body' => 'Dear [vendor],'. PHP_EOL . PHP_EOL .'Your pickup on behalf of [shipper_name] is [status]' . PHP_EOL . PHP_EOL . 'Please contact TRAX for further details' . PHP_EOL . PHP_EOL . 'info@trax.pk' . PHP_EOL . PHP_EOL . '0213-877-22-22', 'updated_by' => 3, 'status' => 0),
            array('id' => 51, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pickup Request Done/Not Done', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [vendor],'. PHP_EOL . PHP_EOL .'Your pickup on behalf of [shipper_name] is [status]' . PHP_EOL . PHP_EOL . 'Please contact TRAX for further details' . PHP_EOL . PHP_EOL . 'info@trax.pk' . PHP_EOL . PHP_EOL . '0213-877-22-22', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
