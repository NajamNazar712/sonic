<?php

namespace App\Console\Commands;

use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentCalculation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class ShipperPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipper:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Calculate Shipper's Pending, Process and Paid Payments";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $to_date = Carbon::now()->format('Y-m-d 23:59:59');
        $from_date = Carbon::now()->subDays(7)->format('Y-m-d 00:00:00');
        $shipper_id = Auth::id();
        $payment_ids = DonePayment::where('user_id', $shipper_id);

        $pending_payment_ids = PendingPayment::where('user_id', $shipper_id)->pluck('id')->toArray();

        $paid_payment_ids = $payment_ids->where('status', 1)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->pluck('id')->toArray();

        $process_payment_ids = $payment_ids->where('status', 0)->pluck('id')->toArray();

        $total_process_payments = DonePaymentCalculation::whereIn('done_payment_id', $process_payment_ids)->sum('payable');
        $total_paid_payments = DonePaymentCalculation::whereIn('done_payment_id', $paid_payment_ids)->sum('payable');
        $total_pending_payments = PendingPaymentCalculation::whereIn('pending_payment_id', $pending_payment_ids)->sum('payable');

        return (['process_payments' => $total_process_payments, 'pending_payments' => $total_pending_payments, 'paid_payments' => $total_paid_payments]);

    }
}
