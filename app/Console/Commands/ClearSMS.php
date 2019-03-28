<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Models\SMS;

use App\Jobs\ProcessSMS;

class ClearSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear SMS';

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
        $datetime = Carbon::now()->subDays(30);

        SMS::where('created_at', '<', $datetime)->delete();

        $datetime = Carbon::now()->subHours(1);

        SMS::where('status', 1)->where('created_at', '<', $datetime)->update(['status' => 2]);

        $smses = SMS::where('status', 1);

        if ($smses->exists()) {
            $smses = $smses->get();

            foreach ($smses as $sms) {
                dispatch(new ProcessSMS($sms));
            }
        }
    }
}
