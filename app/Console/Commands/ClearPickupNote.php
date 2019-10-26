<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Models\PickupNote;

class ClearPickupNote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pickupnote:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Pickup Note';

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
        $pickup_notes = PickupNote::where('status_id', 3);

        if ($pickup_notes->exists()) {
            $pickup_notes = $pickup_notes->get();

            foreach ($pickup_notes as $pickup_note) {
                $completed = TRUE;

                $pickup_note_requests = $pickup_note->pickup_note_requests;

                if ($pickup_note_requests) {
                    foreach ($pickup_note_requests as $pickup_note_request) {
                        $pickup_request = $pickup_note_request->pickup_request;

                        if ($pickup_request->status < 2) {
                            $completed = FALSE;

                            break;
                        }
                    }
                }

                if ($completed) {
                    $pickup_note->status_id = 4;

                    $pickup_note->save();
                }
            }
        }
    }
}
