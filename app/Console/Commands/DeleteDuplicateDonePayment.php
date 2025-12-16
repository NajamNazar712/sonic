<?php

namespace App\Console\Commands;

use App\Http\Models\PendingPayment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteDuplicateDonePayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:duplicate_done_delivered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $startDate =  Carbon::now()->subDays(60)->format('Y-m-d 00:00:00');
        $startDate2 =  Carbon::now()->subDays(30)->format('Y-m-d 00:00:00');
        $endDate = Carbon::now()->format('Y-m-d 23:59:59');
        $type = 0;



        $duplicates = DB::table('done_payment_shipments')
            ->selectRaw('MIN(done_payment_shipments.id) AS min_id,done_payments.user_id')
            ->whereBetween('done_payment_shipments.created_at', [$startDate, $endDate])
            ->where('done_payment_shipments.type', $type)
            ->join('done_payments', 'done_payments.id', '=', 'done_payment_shipments.done_payment_id')
            ->join('users', 'users.id', '=', 'done_payments.user_id')
            ->where('done_payments.status',0)
            ->groupBy('done_payment_shipments.shipment_id', 'done_payment_shipments.amount', 'done_payment_shipments.payable', 'done_payment_shipments.charges', 'done_payment_shipments.type')
            ->havingRaw('COUNT(done_payment_shipments.shipment_id) > 1')
            ->havingRaw('COUNT(done_payment_shipments.amount) > 1')
            ->havingRaw('COUNT(done_payment_shipments.payable) > 1')
            ->havingRaw('COUNT(done_payment_shipments.charges) > 1')
            ->havingRaw('COUNT(done_payment_shipments.type) > 1')
            ->pluck('user_id','min_id')->toArray();

        if (count($duplicates) > 0) {
            $duplicate_value = array_keys($duplicates);

            $done_payment_ids = DB::table('done_payment_shipments')
                ->whereIn('id', $duplicate_value)->groupBy('done_payment_id')
                ->select('done_payment_id')->pluck('done_payment_id')->toArray();

            DB::table('done_payment_shipments')->whereIn('id', $duplicate_value)->delete();
            foreach ($done_payment_ids as $value){
                DB::select('CALL update_done_payment_statistics(?)', $value);
            }
            

            echo "Deleted/Update duplicate records successfully.";
        } else {
            echo "No duplicate records found for deletion.";
        }


    }
}
