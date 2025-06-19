<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email_check';

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
        Mail::mailer('huawei_email')->raw('This is a test email from Laravel.', function ($message) {
            $message->to('uit.mohsin95@gmail.com')
                ->from('return@slgtrax.com', 'SLG Trax') // optional name
                ->subject('Laravel Test Email via Huawei');
        });
    }
}
