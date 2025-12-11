<?php

namespace Database\Seeders;

use App\Http\Models\DonePayment;
use App\Models\UserSettledPaymentBank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InsertOldDonePaymentsBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        return true;
        $donePayments = DonePayment::whereNotNULL('user_bank_info_id')->get();

        if ($donePayments->isEmpty()) {
            echo "No old done_payment records found without bank detail.\n";
            return;
        }

        foreach ($donePayments as $payment) {
            // OPTIONAL: If user has saved bank info in the user profile table
            // replace the lines below with user bank values.

            UserSettledPaymentBank::create([
                'user_id'         => $payment->user_id,
                'done_payment_id' => $payment->id,
                'bank_id'         => $payment->shipper_bank->bank_name,
                'bank_branch'   => $payment->shipper_bank->bank_branch,
                'account_title'   => $payment->shipper_bank->account_title,
                'account_number'  => $payment->shipper_bank->account_no,
                'iban'            => $payment->shipper_bank->iban,
            ]);

            echo "Inserted bank detail for done_payment_id: {$payment->done_payment_id}\n";
        }

        echo "Old done_payment bank details successfully inserted.\n";
    }
}
