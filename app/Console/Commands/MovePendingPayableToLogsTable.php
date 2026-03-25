<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class MovePendingPayableToLogsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:payble-to-logs-table';

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

        DB::insert("
            INSERT INTO negative_payable_daily_logs (user_id, amount, payable, created_at, updated_at)
            SELECT 
                pending_payments.user_id,
                ppc.amount,
                ppc.payable,
                ppc.created_at,
                ppc.updated_at
            FROM pending_payments
            JOIN pending_payment_calculations as ppc 
                ON pending_payments.id = ppc.pending_payment_id
        ");

        //return Command::SUCCESS;
    }
}
