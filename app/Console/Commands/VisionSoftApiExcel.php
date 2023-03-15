<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\VisionSoftAPIController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;

class VisionSoftApiExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:visionsoftexcel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vision Soft Excel';

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
        $cod_payment_excel = VisionSoftAPIController::cod_payable_excel();
        $cod_receivable_excel = VisionSoftAPIController::cod_receivable_excel();
        $links = "";

        if(!empty($cod_receivable_excel)) {
            $links .= "<strong>COD Receivables: </strong> <br>" . "<a download='$cod_receivable_excel' href='$cod_receivable_excel' >$cod_receivable_excel</a>" . "<br>";
        }
        if(!empty($cod_payment_excel)) {
            $links .= "<strong>COD Payable: </strong> <br>" . "<a download='$cod_payment_excel' href='$cod_payment_excel' >$cod_payment_excel</a>" . "<br>";
        }
        if(!empty($cod_payment_excel) || !empty($cod_receivable_excel)) {
            NotificationsController::send(213, $links);
        }
    }
}
