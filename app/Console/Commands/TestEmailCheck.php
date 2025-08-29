<?php

namespace App\Console\Commands;

use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Shipment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TestEmailCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email_check {payment_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //        usefull commands
        //    revenue_report_by_user_excel
        //    mark_arrival

        $array = [
            54826986,
            54822944,
            54836886,
            54849066,
            54845140,
            54839200,
            54855387,
            54839211,
            54824791,
            54828330,
            54832051,
            54836438,
            54836780,
            54837219,
            54847670,
            54849673,
            54852769,
            54853133,
            54853822,
            54855393,
            54855611,
            54848861,
            54822813,
            54849796,
            54836546,
            54839437,
        ];

        foreach ($array as $id){
            $shipment = Shipment::find($id);
            if($shipment) {
                $pickup_city_id = $shipment->pickup_address->city_id;
                ShipperShipmentBookController::generate_tracking_number($shipment->id, $pickup_city_id, $shipment->consignee_city_id);
            }
        }
        exit();

        if ($this->hasArgument('payment_id')){
            $paymentId = explode(',', $this->argument('payment_id'));
            $records = DB::table('done_payment_shipments')
                ->whereIn('done_payment_id', $paymentId)
                ->select('id', 'wht', 'cod_sst', 'payable')
                ->get();

            foreach ($records as $record) {
                //            $currentPayable = DB::table('done_payment_shipments')
                //                ->where('id', $record->id)
                //                ->value('payable');


                $currentPayable = $record->payable;
                DB::table('done_payment_shipments')
                    ->where('id', $record->id)
                    ->update(['payable' => $currentPayable + $record->wht + $record->cod_sst, 'cod_sst' => 0, 'wht' => 0]);
            }

            DB::select('CALL update_done_payment_statistics(?)', $paymentId);
        }
//         $records = DB::table('done_payment_shipments')
//             ->whereIn('done_payment_id', [1641598])
//             ->select('id', 'wht', 'cod_sst','payable')
//             ->get();

//         foreach ($records as $record) {
// //            $currentPayable = DB::table('done_payment_shipments')
// //                ->where('id', $record->id)
// //                ->value('payable');


//             $currentPayable = $record->payable;
//             DB::table('done_payment_shipments')
//                 ->where('id', $record->id)
//                 ->update(['payable' => $currentPayable + $record->wht + $record->cod_sst, 'cod_sst' => 0, 'wht' => 0]);

//         }

//         DB::select('CALL update_done_payment_statistics(?)', [1641598]);
        // DB::select('CALL update_done_payment_statistics(?)', [1629765]);
//        DB::select('CALL update_done_payment_statistics(?)', [1624481]);


//        Mail::mailer('huawei_email')->raw('This is a test email from Laravel.', function ($message) {
//            $message->to('uit.mohsin95@gmail.com')
//                ->from('return@slgtrax.com', 'SLG Trax') // optional name
//                ->subject('Laravel Test Email via Huawei');
//        });

    }



}
