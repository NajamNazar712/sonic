<?php

namespace App\Console\Commands;

use App\Http\Controllers\Dashboard\BusinessProjectionRetentionController;
use Illuminate\Console\Command;

class BusinessProjectionAndRetention extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:projectionandretention';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Business Projection and Retention Dashboard Data Update';

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
        BusinessProjectionRetentionController::create_business_projections();
    }
}
