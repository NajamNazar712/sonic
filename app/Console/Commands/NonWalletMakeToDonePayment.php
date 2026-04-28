<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\PendingPayment;
use App\Http\Models\WalletUser;
use App\Models\TPaymentCycle;
use Carbon\Carbon;

class NonWalletMakeToDonePayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'non-wallet-users:make-to-done';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending payments from make to done of non wallet users based on their T Payment Cycle';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now();

        // Payment runs Monday to Friday only
        if ($today->isWeekend()) {
            $this->info('Skipping: today is a weekend.');
            return 0;
        }

        $excludedUserIds = GlobalSettings::where('type', 't_payment_exclude_shippers')->first()->text ?? '';
        $excludedUserIds = array_filter(explode(',', $excludedUserIds));

        $walletUserIds = WalletUser::pluck('user_id')->toArray();

        $pending_payments = PendingPayment::where('is_hold', 0)
            ->whereNotIn('user_id', $excludedUserIds)
            ->whereNotIn('user_id', $walletUserIds)
            ->get();
        

        foreach ($pending_payments as $pending_payment) {
            $user_id = $pending_payment->user_id;

            $tCycle = TPaymentCycle::where('user_id', $user_id)->first();

            $T = $tCycle ? (int) substr($tCycle->value, 2) : 5; // default T-5 if not found
            // Calculate the cutoff date based on today's weekday and T value
            $cutoffDate = $this->calculateCutoffDate($today, $T);
            // Collect shipment IDs whose created_at is on or before the cutoff date
            $filteredShipments = $pending_payment
                ->pending_payment_shipments()
                ->where('created_at', '<=', $cutoffDate)
                ->get(['id', 'payable']);

            // Skip if total payable of filtered shipments is zero or negative
            if ($filteredShipments->sum('payable') <= 0) {
                continue;
            }

            $pending_payment_shipment_ids = $filteredShipments->pluck('id')->toArray();
            if (count($pending_payment_shipment_ids) > 0) {
                $request = new Request([
                    'pending_payment_shipment_ids' => implode(',', $pending_payment_shipment_ids),
                    'company_bank_id' => 29,
                ]);
    
                $controller = new AdminFinanceController;
                $controller->make_payments_store($request);

                $this->info("Processed payment for user_id: {$user_id} | T: {$T} | Cutoff: {$cutoffDate->toDateString()}");
            }
        }

        return 0;
    }

    /**
     * Calculate the cutoff date based on today's weekday and the shipper's T value.
     *
     * Rules:
     *  - Payment runs Mon–Fri. Weekends are skipped (not counted as business days).
     *  - T-0 means "up to and including today's previous business day".
     *  - T-1 means one additional business day back, and so on up to T-5.
     *
     * Examples (T-0 to T-5):
     *  Monday:    T-0 = Friday,    T-1 = Thursday, T-2 = Wednesday, T-3 = Tuesday, T-4 = Monday,   T-5 = Friday (prev week)
     *  Tuesday:   T-0 = Monday,    T-1 = Friday,   T-2 = Thursday,  T-3 = Wednesday,T-4 = Tuesday, T-5 = Monday
     *  Wednesday: T-0 = Tuesday,   T-1 = Monday,   T-2 = Friday,    T-3 = Thursday, T-4 = Wednesday,T-5 = Tuesday
     *  Thursday:  T-0 = Wednesday, T-1 = Tuesday,  T-2 = Monday,    T-3 = Friday,   T-4 = Thursday, T-5 = Wednesday
     *  Friday:    T-0 = Thursday,  T-1 = Wednesday,T-2 = Tuesday,   T-3 = Monday,   T-4 = Friday,   T-5 = Thursday
     */
    private function calculateCutoffDate(Carbon $today, int $T): Carbon
    {
        // Start from yesterday (T-0 baseline = last business day)
        // and step back T more business days.
        $stepsBack = $T + 1; // +1 because T-0 already means "previous business day"
        $date = $today->copy();

        for ($i = 0; $i < $stepsBack; $i++) {
            $date->subDay();
            // Skip over Saturday and Sunday
            while ($date->isWeekend()) {
                $date->subDay();
            }
        }

        // Set to end of that day so all shipments created on that day are included
        return $date->endOfDay();
    }
}
