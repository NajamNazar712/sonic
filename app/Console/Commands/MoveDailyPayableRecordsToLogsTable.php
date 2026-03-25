<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;


class MoveDailyPayableRecordsToLogsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:daily-payable-records-to-logs-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command will run every 3 hours and move data of payble into logs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

         DB::insert("
            INSERT INTO negative_payable_daily_logs (user_id, amount, payable, created_at, updated_at)
            SELECT 
                pending_payments.user_id,
                ppc.amount,
                ppc.payable,
                NOW(), NOW()
            FROM pending_payments
            JOIN pending_payment_calculations as ppc 
                ON pending_payments.id = ppc.pending_payment_id
        ");
        //return Command::SUCCESS;
    }
}
