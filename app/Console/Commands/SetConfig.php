<?php

namespace App\Console\Commands;

use App\Helpers\Providers;
use App\Helpers\Strings;
use App\Models\Configuration;
use Illuminate\Console\Command;

class SetConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
        app:set-config
            {option? : The key of the config option to set (you can leave empty to get auto complete).}
            {value? : The new value for the config option}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the value of a configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $option = $this->argument('option');
        $value = $this->argument('value');

        /**
         * Load current configuration
         */
        $configs = Providers::config(null);

        /**
         * Request for option if missing
         */
        $option ??= $this->choice(
            'What do you want to configure?',
            $configs->keys()->toArray(),
            0,
            2
        );

        /**
         * Request for value if missing
         */
        $value ??= $this->ask(
            'What do you want to set as the value for ' . $option . '?',
        );

        $type = Configuration::where('key', $option)->pluck('type')->first();

        /**
         * Check if the provided option is valid
         */
        if (!$type) {
            $this->error(__(':0 is not a valid configuration option', [$option]));
            return 1;
        }

        /**
         * Check if the provided value is valid
         */
        $validate = match (true) {
            in_array($type, ['json', 'array']) => is_array($value) || Strings::jsonValidate($value),
            in_array($type, ['string', 'text']) => is_string($value),
            in_array($type, ['bool', 'boolean']) => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            in_array($type, ['number', 'integer', 'int']) => filter_var($value, FILTER_VALIDATE_INT),
            default => false,
        };

        if (!$validate) {
            $type = in_array($type, ['json', 'array']) ? 'json' : $type;
            $this->error(__(':1 is not a valid :2 value for :0', [$option, $value, $type]));
            return 1;
        }

        if (in_array($type, ['json', 'array'])) {
            $value = json_decode(str($value)->replace(['"[', ']"', '"{', '}"', "'"], ['[', ']', '{', '}', '"'])->toString());
        }

        /**
         * Save the configuration
         */
        $configs = Providers::config([$option => $value]);

        /**
         * Prepare the response value
         */
        $value = Providers::config($option);
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->info(__('New configuration value for :0 is :1', [$option, $value]));
        return 0;
    }
}