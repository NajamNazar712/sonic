<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class FintechSmsNotification extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notifications')->insert(array(
            array(
                'id'               =>  217, 
                'created_at'       =>  Carbon::now(), 
                'updated_at'       =>  Carbon::now(), 
                'name'             => 'Consignee Payment Received - Fintech', 
                'type_id'          =>  2, 
                'body'             => 'Dear [rider],Consignee has paid the COD Amount RS: [amount], Your tip RS: [tip] of this shipment [tracking_number].',
                'updated_by'       => '7'
            ),  

        ));
    }
}
