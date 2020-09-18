<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShortReceivedMasterCargoBag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:shortreceivedmastercargobag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Short Received Shipments of Master Cargo and Bag';

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
        //
    }
}
