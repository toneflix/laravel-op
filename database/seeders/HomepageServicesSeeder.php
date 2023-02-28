<?php

namespace Database\Seeders;

use App\Models\v1\Home\HomepageService;
use Illuminate\Database\Seeder;

class HomepageServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HomepageService::truncate();
        HomepageService::insert([
            [
                'slug' => fake()->slug(),
                'title' => fake()->sentence(rand(3, 5)),
                'icon' => 'fa-solid fa-'.['box-checkmark', 'user-check', 'user-tie', 'user-circle', 'user', 'user-friends', 'user-ninja', 'user-secret', 'user-shield', 'user-tag', 'user-tie', 'users'][rand(0, 10)],
                'image' => random_img('images'),
                'image2' => random_img('images'),
                'content' => fake()->paragraph(rand(3, 5)),
            ],
            [
                'slug' => fake()->slug(),
                'title' => fake()->sentence(rand(3, 5)),
                'icon' => 'fa-solid fa-'.['box-checkmark', 'user-check', 'user-tie', 'user-circle', 'user', 'user-friends', 'user-ninja', 'user-secret', 'user-shield', 'user-tag', 'user-tie', 'users'][rand(0, 10)],
                'image' => random_img('images'),
                'image2' => random_img('images'),
                'content' => fake()->paragraph(rand(3, 5)),
            ],
            [
                'slug' => fake()->slug(),
                'title' => fake()->sentence(rand(3, 5)),
                'icon' => 'fa-solid fa-'.['box-checkmark', 'user-check', 'user-tie', 'user-circle', 'user', 'user-friends', 'user-ninja', 'user-secret', 'user-shield', 'user-tag', 'user-tie', 'users'][rand(0, 10)],
                'image' => random_img('images'),
                'image2' => random_img('images'),
                'content' => fake()->paragraph(rand(3, 5)),
            ],
            [
                'slug' => fake()->slug(),
                'title' => fake()->sentence(rand(3, 5)),
                'icon' => 'fa-solid fa-'.['box-checkmark', 'user-check', 'user-tie', 'user-circle', 'user', 'user-friends', 'user-ninja', 'user-secret', 'user-shield', 'user-tag', 'user-tie', 'users'][rand(0, 10)],
                'image' => random_img('images'),
                'image2' => random_img('images'),
                'content' => fake()->paragraph(rand(3, 5)),
            ],
        ]);
    }
}
