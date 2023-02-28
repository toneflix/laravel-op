<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (\App\Models\v1\User::whereUsername('admin')->doesntExist()) {
            \App\Models\v1\User::insert([
                [
                    'firstname' => 'Super',
                    'lastname' => 'Admin',
                    'username' => 'admin',
                    'address' => '31 Somewhere in Kaduna',
                    'dob' => Carbon::now()->subYears(43),
                    'email' => 'admin@greysoft.ng',
                    'email_verified_at' => now(),
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'remember_token' => \Str::random(10),
                    'role' => 'admin',
                    'privileges' => json_encode(['admin']),
                ],
            ]);
        }
        // \App\Models\v1\User::factory(30)->create();
    }
}
