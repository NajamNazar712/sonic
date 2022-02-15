<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\LastMileDebriefingController;
use App\Http\Models\Admin\AgentDay;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoEndSessionAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:endSession';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto End Session Of Debriefing Agent';

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
        $today = Carbon::now()->format('Y-m-d');
        $agents = AgentDay::where('status','!=',3)->where('date',$today)->get();

        foreach ($agents as $agent)
        {
            $previous_status = $agent->status;
            $agent->status = 3;
            $agent->auto_close = 1;
            $agent->save();

            LastMileDebriefingController::create_day_log($agent->id,$previous_status);
        }
    }
}
