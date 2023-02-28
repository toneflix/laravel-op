<?php

namespace Database\Seeders;

use App\Models\v1\Home\HomepageSlide;
use Illuminate\Database\Seeder;

class HomepageSlidesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HomepageSlide::truncate();
        HomepageSlide::insert([
            [
                'homepage_id' => 1,
                'title' => fake()->sentence(rand(3, 5)),
                'subtitle' => fake()->sentence(rand(3, 5)),
                'slug' => fake()->slug(),
                'color' => '--q-red',
                'image' => random_img('images'),
            ],
            [
                'homepage_id' => 1,
                'title' => fake()->sentence(rand(3, 5)),
                'subtitle' => fake()->sentence(rand(3, 5)),
                'slug' => fake()->slug(),
                'color' => '--q-red',
                'image' => random_img('images'),
            ],
            [
                'homepage_id' => 1,
                'title' => fake()->sentence(rand(3, 5)),
                'subtitle' => fake()->sentence(rand(3, 5)),
                'slug' => fake()->slug(),
                'color' => '--q-red',
                'image' => random_img('images'),
            ],
            [
                'homepage_id' => 1,
                'title' => fake()->sentence(rand(3, 5)),
                'subtitle' => fake()->sentence(rand(3, 5)),
                'slug' => fake()->slug(),
                'color' => '--q-red',
                'image' => random_img('images'),
            ],
        ]);
    }
}
