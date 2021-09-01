<?php

namespace App\Console\Commands;

use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentCalculation;
use App\http\Models\Shipper\ShipperPayment;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class ShipperPaymentCalculation extends Command
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
        $users = User::where('status', 3);
        if ($users->exists()){
            $users = $users->get();
            foreach ($users as $user){
                $total_pending_payments = 0;
                $total_paid_payments = 0;
                $total_process_payments = 0;

                $to_date = Carbon::now()->format('Y-m-d 23:59:59');
                $from_date = Carbon::now()->subDays(7)->format('Y-m-d 00:00:00');
                $shipper_id = $user->id;

                $payment_ids = DonePayment::where('user_id', $shipper_id);
                if ($payment_ids->exists()){
                    $paid_payment_ids = $payment_ids->where('status', 1)
                        ->whereBetween('created_at', [$from_date, $to_date])
                        ->pluck('id')->toArray();
                    $process_payment_ids = $payment_ids->where('status', 0)->pluck('id')->toArray();
                    $total_process_payments = DonePaymentCalculation::whereIn('done_payment_id', $process_payment_ids)->sum('payable');
                    $total_paid_payments = DonePaymentCalculation::whereIn('done_payment_id', $paid_payment_ids)->sum('payable');
                }
                $pending_payment = PendingPayment::where('user_id', $shipper_id);
                if($pending_payment->exists()){
                    $pending_payment_ids = $pending_payment->pluck('id')->toArray();
                    $total_pending_payments = PendingPaymentCalculation::whereIn('pending_payment_id', $pending_payment_ids)->sum('payable');
                }
                $shipper_payment = ShipperPayment::where('user_id', $shipper_id);
                if($shipper_payment->exists()){
                    $shipper_payment = $shipper_payment->first();
                }
                else{
                    $shipper_payment = new ShipperPayment();
                    $shipper_payment->user_id = $shipper_id;
                }
                $shipper_payment->total_pending = $total_pending_payments;
                $shipper_payment->total_process = $total_process_payments;
                $shipper_payment->total_paid = $total_paid_payments;
                $shipper_payment->save();
            }
        }
    }
}
