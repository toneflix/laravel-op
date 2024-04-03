<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Configuration::truncate();
        Configuration::insert([
            [
                'key' => 'app_name',
                'title' => 'App Name',
                'value' => config('app.name'),
                'type' => 'text',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => '',
            ],
            [
                'key' => 'app_email',
                'title' => 'Site Email',
                'value' => 'support@toneflix.com.ng',
                'type' => 'email',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => '',
            ],
            [
                'key' => 'allow_default_images',
                'title' => 'Allow Default Images',
                'value' => true,
                'type' => 'boolean',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => 'Determines wether default images or null should be used in place of missing images',
            ],
            [
                'key' => 'prefered_sms_channel',
                'title' => 'Prefered SMS Channel',
                'value' => 'TWILLIO',
                'type' => 'string',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => 'Which channel should be prefered when sending SMS',
            ],
            [
                'key' => 'prefered_notification_channels',
                'title' => 'Prefered Notification Channel',
                'value' => json_encode(['mail']),
                'type' => 'array',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => 'Which channel should be prefered when sending out notifications',
            ],
            [
                'key' => 'verify_email',
                'title' => 'Verify Email',
                'value' => false,
                'type' => 'boolean',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => 'Whether to enforce user email verification',
            ],
            [
                'key' => 'verify_phone',
                'title' => 'Verify Phone',
                'value' => false,
                'type' => 'boolean',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => 'Whether to enforce user phone number verification',
            ],
            [
                'key' => 'token_lifespan',
                'title' => 'Token Lifespan',
                'value' => 300,
                'type' => 'number',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => 'How long tokens should live (secs.)',
            ],
        ]);
    }
}
