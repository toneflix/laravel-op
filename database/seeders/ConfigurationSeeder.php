<?php

namespace Database\Seeders;

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
        \App\Models\v1\Configuration::truncate();
        \App\Models\v1\Configuration::insert([
            [
                'key' => 'site_name',
                'title' => 'Site Name',
                'value' => 'Laravel OP',
                'type' => 'string',
                'count' => null,
                'max' => null,
            ],
        ]);
    }
}