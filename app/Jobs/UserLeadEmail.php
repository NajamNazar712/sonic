<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\NotificationsController;

class UserLeadEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $leads_added;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($leads_added)
    {
        $this->leads_added = $leads_added;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        NotificationsController::send(230, $this->leads_added);

    }
}
