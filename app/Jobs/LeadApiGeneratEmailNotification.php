<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\NotificationsController;

class LeadApiGeneratEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $lead;
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($new_lead)
    {
       $this->lead = $new_lead;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

          try {
                NotificationsController::send(113, $this->lead);
                if($this->lead->sales_person_id)
                {
                    NotificationsController::send(204, $this->lead);
                }
          } catch (\Throwable $th) {
               //if any exception than try again after 10 
                Log::error('An error occurred LeadApiController Email Sending: ' . $e->getMessage());
               $this->release(20);
          }        
    }
}
