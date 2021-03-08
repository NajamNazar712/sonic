<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForConsigneeAddress extends Seeder
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
            array('id' => 126, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Consignee Address', 'type_id' => 2, 'subject' => null, 'body' => 'مھزز کنسائنی :'.PHP_EOL.'براۓ مربانی اپنا پارسل [tracking_number] ہمارے آفس سے دو (2) دن کے اندر لے لیں ۔ بصورت دیگر آپکا  پارسل واپس بیہج دیا جایگا '.PHP_EOL.PHP_EOL.':ایڈرس' .PHP_EOL. '[location]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
