<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\AccountReconciliationController;
use Illuminate\Console\Command;

class PushBackupDatatoamazon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:amazon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Storage backup to s3';

    /**
     * Create a new command instance.
     *
     * @return void


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }
}
