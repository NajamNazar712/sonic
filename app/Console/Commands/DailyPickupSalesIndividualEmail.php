<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DailyPickupSalesIndividualEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:dailypickupsalesreportindividual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Daily Pickup & Sales Report Email to Sales Persons';

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

    }
}
