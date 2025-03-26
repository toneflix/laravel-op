<?php

namespace App\Console\Commands;

use App\Services\SimpleDataExporter;
use Illuminate\Console\Command;

class ExportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export
                            {--Q|queue}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Helps prepare and export generic model data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new SimpleDataExporter(50))->export();

        return 0;
    }
}
