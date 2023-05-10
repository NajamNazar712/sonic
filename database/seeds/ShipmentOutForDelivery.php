<?php
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ShipmentOutForDelivery extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notifications')->where('id',12)->delete();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert([
            'id'            => 12,
            'created_at'    => $timestamp,
            'updated_at'    => $timestamp,
            'name'          => 'Shipment Out for Delivery for Consignee',
            'type_id'       => 2,
            'body'          => 'Dear [consignee_name],' . PHP_EOL . 'Your order from [company_name] is Out for Delivery under [tracking_number]. Please keep [amount] ready for collection.' . PHP_EOL . 'click to pay online : [online_payment_link]'  . PHP_EOL . PHP_EOL . PHP_EOL . 'Please contact at info@trax.pk or 0304-11-11-232 for further details.', 
            'status'        => 0,
            'updated_by'    => 3,
        ]);

    }
}
