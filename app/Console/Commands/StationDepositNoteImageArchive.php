<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\DeliveryController;
use Illuminate\Console\Command;

class StationDepositNoteImageArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:stationdepositnoteimage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive station deposit note images archive';

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
        DeliveryController::sdn_archive_directory();
    }
}
