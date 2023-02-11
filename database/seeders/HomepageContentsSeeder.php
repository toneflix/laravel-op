<?php

namespace Database\Seeders;

use App\Models\v1\Home\HomepageContent;
use Illuminate\Database\Seeder;

class HomepageContentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HomepageContent::truncate();
        HomepageContent::insert([
            [
                'homepage_id' => 1,
                'slug' => str('About Us')->slug(),
                'title' => 'About Us',
                'subtitle' => 'Who we are',
                'leading' => 'We are doing wonderful things',
                'image' => random_img('images'),
                'image2' => random_img('images'),
                'linked' => true,
                'parent' => null,
                'attached' => json_encode([]),
                'content' => 'Laravel OP is a simple fork of Laravel that provides a set of tools to help you build your Laravel application faster and easier. It is a collection of useful classes, traits, and functions that I have found useful in my own projects. It is a work in progress and I will be adding more features as I need them. I hope you find it useful too.',
                'iterable' => false,
            ],
            [
                'homepage_id' => 1,
                'slug' => str('Our Services')->slug(),
                'title' => 'Services',
                'subtitle' => 'Our Services',
                'leading' => 'We are doing wonderful things',
                'image' => null,
                'image2' => null,
                'linked' => true,
                'parent' => null,
                'attached' => json_encode(['HomepageService']),
                'content' => null,
                'iterable' => true,
            ],
            [
                'homepage_id' => 1,
                'slug' => str('Community')->slug(),
                'title' => 'Community',
                'subtitle' => 'Community',
                'leading' => 'We are doing wonderful things',
                'image' => random_img('images'),
                'image2' => random_img('images'),
                'linked' => true,
                'parent' => null,
                'attached' => json_encode([]),
                'content' => fake()->sentences(10, true),
                'iterable' => false,
            ],
        ]);
    }
}