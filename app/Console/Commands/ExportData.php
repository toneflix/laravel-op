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
                            {dataset?* : List of exportable dataset (Allowed options are the id defined in your exports.)}
                            {--Q|queue : Queue the process for later}
                            {--e|emails=* : Email addresses that should get exported data notification}
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
        (new SimpleDataExporter(
            perPage: 50,
            emails: [],
            dataset: $this->argument('dataset'),
        ))->export();

        return 0;
    }
}
