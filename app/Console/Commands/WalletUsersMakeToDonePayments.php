<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Models\WalletUser;
use App\Http\Models\PendingPaymentShipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Admin\GlobalSettings;
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
        $today = Carbon::now();

        if (!$today->isSunday()) {
            // $setting = GlobalSettings::where('type', 'wallet_user_make_to_done_stop')->first();

            // $excludedUserIds = $setting && $setting->text ? array_filter(explode(',', $setting->text)) : '';
            
            // if(!empty($excludedUserIds)){
                $wallet_user_ids = WalletUser::where([
                    'status' => 1,
                    'substitute_user_id' => 0
                ])
                ->where('finova_account_type', '>', 0)
                ->whereNotIn('user_id', [46611, 47392, 47394, 47813, 33952, 27424, 44309, 44149, 3719, 2634, 18179, 49456, 043576, 22071, 2121, 39282, 49028, 12240, 1458, 7626, 23009,27519,25229, 4429,46539, 4251,35393,44233, 25231]) // Remove Test Shipper
                ->pluck('user_id')
                ->toArray();
    
                $today_is_sunday = Carbon::now();
    
                foreach($wallet_user_ids as $wallet_user_id) {
                    if($wallet_user_id != 23009) {
                        $pending_payment = PendingPayment::with(['pending_payment_shipments' => function ($query) {
                            $query->where('created_at', '<', Carbon::today()->format('Y-m-d H:i:s'));
                        }])
                            ->where('user_id', $wallet_user_id)
                            ->where('created_at', '<', Carbon::today()->format('Y-m-d H:i:s'))
                            ->first();


                        if ($pending_payment && $pending_payment->pending_payment_shipments->sum('payable') > 0) {

                            $pending_payment_shipment_ids = $pending_payment->pending_payment_shipments->pluck('id')->toArray();

                            $request = new Request(['pending_payment_shipment_ids' => implode(',', $pending_payment_shipment_ids), 'company_bank_id' => 48]);

                            $controller = new AdminFinanceController;
                            $controller->make_payments_store($request);
                        }
                    }
    
                }
            // }
        }

        
    }
}
