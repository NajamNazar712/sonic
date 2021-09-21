<?php

use Illuminate\Database\Seeder;

class AddAppNotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('app_notifications')->insert(array(
            array('id' => 1, 'name' => 'Pickup Request Reassigned', 'title' => 'Pickup Request Reassigned', 'body' => 'Dear Rider Pickup of [shipper_name] Has Been Reassigned To You From [rider]', 'app_id' => 1, 'updated_by' => 7),
            array('id' => 2, 'name' => 'Pickup Request Reassigned', 'title' => 'Pickup Request Reassigned', 'body' => 'Dear Rider Pickup of [shipper_name] Has Been Reassigned To [rider]', 'app_id' => 1, 'updated_by' => 7),
            array('id' => 3, 'name' => 'Pickup Request Assigned', 'title' => 'Pickup Request Assigned', 'body' => 'Dear Rider Pickup of [shipper_name] Has Been Assigned To You', 'app_id' => 1, 'updated_by' => 7),
            array('id' => 4, 'name' => 'Return Note Assigned', 'title' => 'Return Note Assigned', 'body' => 'Dear Rider Return Note # [note_id] Has Been Assigned To You', 'app_id' => 1, 'updated_by' => 7),
            array('id' => 5, 'name' => 'Delivery Note Assigned', 'title' => 'Delivery Note Assigned', 'body' => 'Dear Rider Delivery Note # [note_id] Has Been Assigned To You', 'app_id' => 1, 'updated_by' => 7),
            array('id' => 6, 'name' => 'Pickup Request Assigned', 'title' => 'Pickup Request Assigned', 'body' => 'Dear Rider Pickup of [shipper_name] Has Been Auto Assigned To You', 'app_id' => 1, 'updated_by' => 7),
        ));
    }
}
