<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\ReturnController;
use Illuminate\Console\Command;

class ReturnNoteImageArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:returnnoteimage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive Return Note Image';

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
        ReturnController::archive_directory();
    }
}
