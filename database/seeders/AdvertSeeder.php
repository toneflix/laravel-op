<?php

namespace Database\Seeders;

use App\Models\v1\Advert;
use Illuminate\Database\Seeder;

class AdvertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adverts = [
            [
                'icon' => 'fa-solid fa-box',
                'title' => '{user} you\'re doing great!',
                'details' => 'Today is a good day to make money',
                'media' => random_img('images'),
                'url' => null,
                'meta' => [
                    'align' => 'start',
                    'justify' => 'end',
                    'welcome' => true,
                    'dummy' => true,
                ],
                'places' => [
                    'users',
                ],
                'active' => true,
            ],
        ];

        // Delete all adverts having meta->dummy = true
        Advert::where('meta->dummy', true)->delete();
        // Free auto-incrementing id
        if (Advert::max('id') > 0) {
            \DB::statement('ALTER TABLE adverts AUTO_INCREMENT = ?;', [Advert::max('id') + 1]);
        } else {
            \DB::statement('ALTER TABLE adverts AUTO_INCREMENT = 1;');
        }

        // Seed the database
        foreach ($adverts as $advert) {
            Advert::create($advert);
        }
    }
}