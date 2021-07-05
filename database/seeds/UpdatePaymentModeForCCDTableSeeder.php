<?php

use Illuminate\Database\Seeder;
use App\Http\Models\PaymentMode;

class UpdatePaymentModeForCCDTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment_mode = PaymentMode::find(2);
        $payment_mode->mode = 'Credit Card on Delivery-CCD';
        $payment_mode->save();
    }
}
