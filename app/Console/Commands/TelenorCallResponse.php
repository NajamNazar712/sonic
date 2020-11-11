<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelenorCallApiController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TelenorCallResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telenor:callresponse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Response against Call ID(s)';

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
        $date = Carbon::today()->format('Y-m-d');
        TelenorCallApiController::response_call($date);
    }
}
