<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelenorCallApiController;
use Illuminate\Console\Command;

class TelenorCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telenor:call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call to consignee for response';

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
        TelenorCallApiController::call();
    }
}
