<?php

namespace App\Console\Commands;

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
        if ($this->hasArgument('payment_id')){
            $records = DB::table('done_payment_shipments')
                ->whereIn('done_payment_id', [$this->hasArgument('payment_id')])
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

            DB::select('CALL update_done_payment_statistics(?)', [$this->hasArgument('payment_id')]);
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
