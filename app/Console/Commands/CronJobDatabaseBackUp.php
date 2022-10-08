<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

class CronJobDatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'backup database';

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
     * @return int
     */
    public function handle()
    {
        $backUpPath = "C:/BackupDB/";

        $DUMP_PATH = 'C:\AppServ\MySQL\bin\mysqldump.exe';

        for ($a = 2; $a <= 7; $a++) {
            $n = $a - 1;
            $newFileName = $backUpPath . "db_api_edge_cluster-" . $n . ".sql";
            $file = $backUpPath . "db_api_edge_cluster-" . $a . ".sql";
            rename($file, $newFileName);
        }

        $filename = "db_api_edge_cluster-7.sql";
        $command = "" . $DUMP_PATH . " --no-defaults --user=" . env('DB_USERNAME') . " --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  > " . $backUpPath . $filename;

        $returnVar = NULL;
        $output = NULL;

        exec($command, $output, $returnVar);
    }
}
