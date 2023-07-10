<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class FintechAppNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('app_notifications')->insert(array(
            array(
                'id'               =>  21, 
                'created_at'       =>  Carbon::now(), 
                'updated_at'       =>  Carbon::now(), 
                'name'             => 'Consignee Payment Received - Fintech', 
                'title'            => '[tracking_number] Payment Received [amount]', 
                'body'             => 'Dear [rider],Consignee has paid the COD Amount RS: [amount], Your tip RS: [tip] of this shipment [tracking_number].',
                'app_id'           => '1',
                'updated_by'       => '664'
            ),  

        ));
    }
}
