<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MakePolicies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
        app:make-policies
            {exclude?* : Models to exclude (Don\'t include path and extension)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates policies for every model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $exclude = (array)$this->argument('exclude');
        $files = collect(File::files(app_path('Models')));

        $fileList = $files
            ->filter(fn($file) => $file->getExtension() === 'php')
            ->map(fn($file) => str($file->getFilename())->remove('.php')->toString());

        $defaultExcludes = ['Configuration', 'User', 'File', 'PasswordCodeResets', 'TempUser', 'Transaction'];


        if (!count($exclude) && app()->runningInConsole()) {
            $defModels = $fileList->where(fn($name) => in_array($name, $defaultExcludes))->keys()->join(', ');
            $exclude = $this->choice('Choose models to exclude', $fileList->toArray(), $defModels, null, true);
            $exclude = array_merge($exclude, $defaultExcludes);
        } else {
            $exclude = $defaultExcludes;
        }

        $count = $fileList->filter(function ($fn) use ($exclude) {
            return ! File::exists(app_path("Models/Policies/{$fn}Policy.php")) &&
                ! File::exists(app_path("Policies/{$fn}Policy.php")) &&
                ! in_array($fn, $exclude);
        })->each(function ($fn) {
            Artisan::call("make:policy {$fn}Policy --model {$fn} --force");
        })->count();

        $this->info("{$count} policies were successfully generated.");
    }
}