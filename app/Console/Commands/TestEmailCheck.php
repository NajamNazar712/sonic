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
    protected $signature = 'test:email_check';

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
        $records = DB::table('done_payment_shipments_backup')
            ->where('done_payment_id', 1623393)
            ->select('id', 'wht', 'cod_sst')
            ->get();

        foreach ($records as $record) {
            $currentPayable = DB::table('done_payment_shipments')
                ->where('id', $record->id)
                ->value('payable');
            
            DB::table('done_payment_shipments')
                ->where('id', $record->id)
                ->update(['payable' => $currentPayable + $record->wht + $record->cod_sst]);
        }

    }

//        Mail::mailer('huawei_email')->raw('This is a test email from Laravel.', function ($message) {
//            $message->to('uit.mohsin95@gmail.com')
//                ->from('return@slgtrax.com', 'SLG Trax') // optional name
//                ->subject('Laravel Test Email via Huawei');
//        });

}
