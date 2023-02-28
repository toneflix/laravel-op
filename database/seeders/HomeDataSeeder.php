<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomeDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            HomepagesSeeder::class,
            HomepageContentsSeeder::class,
            HomepageSlidesSeeder::class,
            HomepageServicesSeeder::class,
            NavigationSeeder::class,
        ]);
    }
}
