<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RestartSupervisordProcesses extends Command
{
    protected $signature = 'supervisord:restart';
    protected $description = 'Restart Supervisord processes that are not running';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        // Restart non-running supervisor processes
        $command = "supervisorctl status | grep -v RUNNING | awk '{print \$1}' | while read -r process_name; do supervisorctl restart \"\$process_name\"; done";
        $process = Process::fromShellCommandline($command, '/var/www/html/sonic');
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error($process->getErrorOutput());
            return 1;
        }

        $this->info($process->getOutput());

        // Change ownership of storage directory
        $chown = Process::fromShellCommandline('sudo chown -R www-data:www-data storage', '/var/www/html/sonic');
        $chown->run();
        if (!$chown->isSuccessful()) {
            $this->error("chown failed: " . $chown->getErrorOutput());
            return 1;
        }

        // Change permissions of storage directory
        $chmod = Process::fromShellCommandline('sudo chmod -R 775 storage', '/var/www/html/sonic');
        $chmod->run();
        if (!$chmod->isSuccessful()) {
            $this->error("chmod failed: " . $chmod->getErrorOutput());
            return 1;
        }

        $this->info('Storage directory permissions and ownership updated.');
        return 0;
    }


}
