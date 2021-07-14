<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Models\SMS;

use App\Jobs\ProcessOTPSMS;

use Carbon\Carbon;

class RetryOTPSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:retry_otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry OTP SMS';

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
        $datetime = Carbon::now()->subHours(1);

        SMS::where('status', 1)->where('otp', 1)->where('created_at', '<', $datetime)->update(['status' => 2]);

        $smses = SMS::where('status', 1)->where('otp', 1);

        if ($smses->exists()) {
            $smses = $smses->get();

            foreach ($smses as $sms) {
                $sms->status = 0;

                $sms->save();

                dispatch(new ProcessOTPSMS($sms));
            }
        }
    }
}
