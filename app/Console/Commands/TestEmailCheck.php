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
            54967535,
            54967539,
            54967536,
            54967560,
            54967558,
            54967538,
            54967570,
            54967542,
            54967540,
            54967545,
            54967548,
            54967550,
            54967546,
            54967566,
            54967569,
            54967557,
            54967561,
            54967543,
            54967555,
            54967537,
            54967554,
            54967551,
            54967552,
            54967547,
            54967568,
            54954402,
            54954630,
            54962167,
            54954638,
            54962490,
            54962471,
            54918379,
            54967535,
            54967539,
            54967536,
            54967560,
            54967558,
            54967538,
            54967570,
            54967542,
            54967540,
            54967545,
            54967548,
            54967550,
            54967546,
            54967566,
            54967569,
            54967557,
            54967561,
            54967543,
            54967555,
            54967537,
            54967554,
            54967551,
            54967552,
            54967547,
            54967568,
            54954402,
            54954630,
            54962167,
            54954638,
            54962490,
            54962471,
            54918379,
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
