<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\WalletUser;
use App\Http\Models\PendingPaymentShipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\PendingPayment;

class WalletUsersMakeToDonePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet-users:make-to-done';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending payments from make to done of wallet users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $wallet_user_ids = WalletUser::where([
            'status' => 1,
            'substitute_user_id' => 0
        ])
        ->where('finova_account_type', '>', 0)
        ->pluck('user_id')
        ->toArray();

        foreach($wallet_user_ids as $wallet_user_id) {

            $pending_payment = PendingPayment::with('pending_payment_shipments')
            ->where('user_id', $wallet_user_id) 
            ->first(); 
            
            if($pending_payment && $pending_payment->pending_payment_shipments->sum('payable') > 0) {

                $pending_payment_shipment_ids = $pending_payment->pending_payment_shipments->pluck('id')->toArray();
                
                $request = new Request(['pending_payment_shipment_ids' => implode(',',$pending_payment_shipment_ids), 'company_bank_id' => 48]);

                $controller = new AdminFinanceController;
                $controller->make_payments_store($request);
            }
            
        }
        
    }
}
