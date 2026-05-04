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
use App\Http\Models\Shipper\User;

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

        $cashShipperIds = GlobalSettings::where('type', 't_payment_cash_shippers')->first()->text ?? '';
        $cashShipperIds = array_filter(explode(',', $cashShipperIds));

        $walletUserIds = WalletUser::pluck('user_id')->toArray();

        $pending_payments = PendingPayment::where('is_hold', 0)
            ->whereNotIn('user_id', $excludedUserIds)
            ->whereNotIn('user_id', $walletUserIds)
            ->get();


        foreach ($pending_payments as $pending_payment) {
            $user_id = $pending_payment->user_id;

            $user = User::select('id', 'payment_cycle_id', 'payment_cycle_days', 'created_at')
                ->find($user_id);

            // Check whether today matches user's payment cycle
            if (!$this->shouldRunPaymentToday($user, $today)) {
                $this->info("Skipped user_id: {$user_id} | payment cycle does not match today");
                continue;
            }

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
                $companyBankId = in_array($user_id, $cashShipperIds) ? 47 : 29;
                $request = new Request([
                    'pending_payment_shipment_ids' => implode(',', $pending_payment_shipment_ids),
                    'company_bank_id' => $companyBankId,
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

    private function matchesMonthDays(Carbon $today, array $monthDays): bool
    {
        if (count($monthDays) === 0) {
            return false;
        }

        foreach ($monthDays as $day) {
            if ($today->day === (int) $day) {
                return true;
            }
        }

        return false;
    }


    private function shouldRunPaymentToday(User $user, Carbon $today): bool
    {
        $cycleId = (int) $user->payment_cycle_id;
        $rawDays = trim((string) $user->payment_cycle_days);

        switch ($cycleId) {
            case 1: // Daily
                return true;

            case 2: // Weekly
            case 4: // Twice a week
            case 5: // Thrice a week
                $weekDays = $this->parseWeekDays($rawDays);
                return in_array($today->dayOfWeekIso, $weekDays, true);

            case 3: // Monthly => like 7
            case 6: // Fortnight => like 7,22
                $monthDays = $this->parseMonthDays($rawDays);
                return $this->matchesMonthDays($today, $monthDays);

            default:
                return false;
        }
    }

    private function parseWeekDays(string $rawDays): array
    {
        if ($rawDays === '') {
            return [];
        }

        $map = [
            '1' => 1, 'mon' => 1, 'monday' => 1,
            '2' => 2, 'tue' => 2, 'tues' => 2, 'tuesday' => 2,
            '3' => 3, 'wed' => 3, 'wednesday' => 3,
            '4' => 4, 'thu' => 4, 'thur' => 4, 'thurs' => 4, 'thursday' => 4,
            '5' => 5, 'fri' => 5, 'friday' => 5,
            '6' => 6, 'sat' => 6, 'saturday' => 6,
            '7' => 7, 'sun' => 7, 'sunday' => 7,
        ];

        $tokens = explode(',', strtolower($rawDays));

        $days = [];
        foreach ($tokens as $token) {
            $token = trim($token);
            if (isset($map[$token])) {
                $days[] = $map[$token];
            }
        }

        return array_values(array_unique($days));
    }

    private function parseMonthDays(string $rawDays): array
    {
        if ($rawDays === '') {
            return [];
        }

        $tokens = explode(',', $rawDays);

        $days = [];
        foreach ($tokens as $token) {
            $day = (int) trim($token);

            if ($day >= 1 && $day <= 31) {
                $days[] = $day;
            }
        }

        return array_values(array_unique($days));
    }
}
