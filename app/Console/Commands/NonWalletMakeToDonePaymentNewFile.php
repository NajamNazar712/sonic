<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\PendingPayment;
use App\Http\Models\WalletUser;
use App\Models\TPaymentCycle;
use Carbon\Carbon;
use App\Http\Models\Shipper\User;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Http\Request;


class NonWalletMakeToDonePaymentNewFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'non-wallet-users:make-to-done-new-file';

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
        Log::channel('non_wallet_payment_log')->info('Started: Cron Started');
        $today = Carbon::now();

        // Payment runs Monday to Friday only
        if ($today->isWeekend()) {
           Log::channel('non_wallet_payment_log')->info('Skipping: today is a weekend.');
            return 0;
        }

        // Temporary skip for coming Tuesday, Wednesday, Thursday -> due to Eid
        $skipDates = [
            '2026-05-26', // Tuesday
            '2026-05-27', // Wednesday
            '2026-05-28', // Thursday
        ];

        if (in_array($today->toDateString(), $skipDates, true)) {
            Log::channel('non_wallet_payment_log')->info(
                'Skipping: temporary skip date - ' . $today->toDateString()
            );

            return 0;
        }

        // $excludedUserIds = GlobalSettings::where('type', 't_payment_exclude_shippers')->first()->text ?? '';
        // $excludedUserIds = array_filter(explode(',', $excludedUserIds));

        // $cashShipperIds = GlobalSettings::where('type', 't_payment_cash_shippers')->first()->text ?? '';
        // $cashShipperIds = array_filter(explode(',', $cashShipperIds));

        // $walletUserIds = WalletUser::pluck('user_id')->toArray();

        // //enable this when going live with all shippers and disable the below
        // $pending_payments = PendingPayment::where('is_hold', 0)
        //     ->whereNotIn('user_id', $excludedUserIds)
        //     ->whereNotIn('user_id', $walletUserIds)
        //     ->get();

        try {
            $excludedUserIds = GlobalSettings::where('type', 't_payment_exclude_shippers')->first()->text ?? '';
            $excludedUserIds = array_filter(explode(',', $excludedUserIds));

            $cashShipperIds = GlobalSettings::where('type', 't_payment_cash_shippers')->first()->text ?? '';
            $cashShipperIds = array_filter(explode(',', $cashShipperIds));

            $walletUserIds = WalletUser::pluck('user_id')->toArray();

            // enable this when going live with all shippers and disable the below
            $startOfYear = Carbon::now()->startOfYear(); // 2026-01-01 00:00:00
            $currentMoment = Carbon::now();               // Current time right now

            $pending_payments = PendingPayment::where('is_hold', 0)
                ->whereNotIn('user_id', $excludedUserIds)
                ->whereNotIn('user_id', $walletUserIds)
                ->where(function ($query) use ($startOfYear, $currentMoment) {
                    $query->whereBetween('created_at', [$startOfYear, $currentMoment])
                        ->orWhereBetween('updated_at', [$startOfYear, $currentMoment]);
                })
                ->orderBy('id', 'desc')
                ->get();

        } catch (Throwable $th) {
            Log::channel('non_wallet_payment_log')->error('NON_WALLET_PAYMENT_SETUP_FAILED', [
                'error_message' => $th->getMessage(),
                'error_file' => $th->getFile(),
                'error_line' => $th->getLine(),
            ]);

            return 1;
        }

        foreach ($pending_payments as $pending_payment) {
            try {

                Log::channel('non_wallet_payment_log')->info("Payment Loop Enter: {$pending_payment->id}");
                $user_id = $pending_payment->user_id;

                $user = User::select('id', 'payment_cycle_id', 'payment_cycle_days', 'created_at')
                    ->where('blacklist', 0)
                    ->where('status', 3)
                    ->find($user_id);

                if (!$user) {

                    continue;
                }

                // Check whether today matches user's payment cycle
                if (!$this->shouldRunPaymentToday($user, $today)) {
                    Log::channel('non_wallet_payment_log')->info("Skipped user_id: {$user_id} | payment cycle does not match today");
                    continue;
                }

                $tCycle = TPaymentCycle::where('user_id', $user_id)->first();

                $T = $tCycle ? (int) substr($tCycle->value, 2) : 5; // default T-5 if not found
                // Calculate the cutoff date based on today's weekday and T value
                $cutoffDate = $this->calculateCutoffDate($today, $T);
                // Collect shipment IDs whose created_at is on or before the cutoff date
                $filteredShipments = $pending_payment
                    ->pending_payment_shipments()
                    ->join('shipments', 'shipments.id', '=', 'pending_payment_shipments.shipment_id')
                    ->where('pending_payment_shipments.created_at', '<=', $cutoffDate)
                    ->select(
                        'pending_payment_shipments.id',
                        'pending_payment_shipments.shipment_id',
                        'pending_payment_shipments.payable'
                    )
                    ->get();

                // Skip if total payable of filtered shipments is zero or negative
                if ($filteredShipments->sum('payable') <= 0) {
                    Log::channel('non_wallet_payment_log')->info("Skipped payment_id: {$pending_payment} | due to zero and negative");
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
                }

            } catch (Throwable $th) {
                    Log::channel('non_wallet_payment_log')->error('NON_WALLET_PAYMENT_FAILED', [
                        'user_id' => $user_id ?? null,
                        'pending_payment_id' => $pending_payment->id ?? null,
                        'shipment_count' => isset($pending_payment_shipment_ids) ? count($pending_payment_shipment_ids) : 0,
                        'company_bank_id' => $companyBankId ?? null,
                        'T' => $T ?? null,
                        'cutoff_date' => isset($cutoffDate) ? $cutoffDate->toDateString() : null,
                        'error_message' => $th->getMessage(),
                        'error_file' => $th->getFile(),
                        'error_line' => $th->getLine(),
                    ]);

                continue;
            }
        }

        return 0;
    }

    /**
     * Calculate cutoff date based on T value.
     *
     * Rule:
     * - Only Saturday is skipped.
     * - Sunday is treated as a valid day.
     * - T-0 means previous day.
     * - T-1 means one more valid day back.
     *
     * Example:
     * Monday:
     *   T-0 = Sunday
     *   T-1 = Friday
     *   T-2 = Thursday
     */
    private function calculateCutoffDate(Carbon $today, int $T): Carbon
    {
        // T-0 = previous day
        // T-1 = one more valid day back
        // Only Saturday is skipped, Sunday is allowed

        $stepsBack = $T + 1;
        $date = $today->copy();

        for ($i = 0; $i < $stepsBack; $i++) {
            $date->subDay();

            // Skip only Saturday
            while ($date->dayOfWeek === Carbon::SATURDAY) {
                $date->subDay();
            }
        }

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
