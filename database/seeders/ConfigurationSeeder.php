<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cache::forget('configuration::build');
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
                'secret' => false,
            ],
            [
                'key' => 'app_email',
                'title' => 'App Email',
                'value' => 'support@toneflix.com.ng',
                'type' => 'email',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => '',
                'secret' => false,
            ],
            [
                'key' => 'app_currency',
                'title' => 'App Currency',
                'value' => 'USD',
                'type' => 'string',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => '',
                'secret' => false,
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
                'secret' => false,
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
                'secret' => false,
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
                'secret' => false,
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
                'secret' => false,
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
                'secret' => false,
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
                'secret' => false,
            ],
            [
                'key' => 'stripe_secret_key',
                'title' => 'Stripe API Secret Key',
                'value' => env('STRIPE_SECRET_KEY'),
                'type' => 'string',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => null,
                'secret' => true,
            ],
            [
                'key' => 'paystack_secret_key',
                'title' => 'Paystack API Secret Key',
                'value' => env('PAYSTACK_SECRET_KEY'),
                'type' => 'string',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => null,
                'secret' => true,
            ],
            [
                'key' => 'paystack_public_key',
                'title' => 'Paystack API Public Key',
                'value' => env('PAYSTACK_PUBLIC_KEY'),
                'type' => 'string',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => null,
                'secret' => false,
            ],
            [
                'key' => 'payment_verify_url',
                'title' => 'Payment Verify URL',
                'value' => 'http://example.com',
                'type' => 'string',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => 'User will be redirected here once payment is successfull',
                'secret' => false,
            ],
            [
                'key' => 'reference_prefix',
                'title' => 'Reference Prefix',
                'value' => 'LOP-',
                'type' => 'string',
                'count' => null,
                'max' => null,
                'col' => 6,
                'autogrow' => false,
                'hint' => 'Will be prepend to every reference string.',
                'secret' => false,
            ],
        ]);
    }
}
