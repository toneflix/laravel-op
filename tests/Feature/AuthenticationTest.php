<?php

namespace Tests\Feature;

use App\Models\PasswordCodeResets;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     */
    public function test_unknown_user_will_not_be_found(): void
    {
        $response = $this->withCredentials()
            ->post(
                '/api/auth/login/',
                [
                    'email' => $this->faker('En-NG')->freeEmail,
                    'password' => 'password'
                ],
                [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'accept' => 'application/json',
                ]
            );

        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->collect('errors'));
    }

    /**
     */
    public function test_user_can_register(): void
    {
        $response = $this->withCredentials()
            ->post(
                '/api/auth/register/',
                [
                    'email' => $this->faker('en-NG')->freeEmail,
                    'lastname' => $this->faker('en-NG')->lastName,
                    'firstname' => $this->faker('en-NG')->firstName,
                    'password' => 'password',
                    'password_confirmation' => 'password'
                ],
                [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'accept' => 'application/json',
                ]
            );

        $response->assertStatus(201);
        $this->assertArrayHasKey('id', $response->collect());
    }

    /**
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create();

        $response = $this->withCredentials()
            ->post(
                '/api/auth/login/',
                [
                    'email' => $user->email,
                    'password' => 'password'
                ],
                [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'accept' => 'application/json',
                ]
            );

        $response->assertStatus(200);
        $this->assertArrayHasKey('id', $response->collect());
    }

    /**
     */
    public function test_user_can_logout(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $response = $this->withHeader('X-Requested-With', 'XMLHttpRequest')->withCredentials()
            ->post('/api/account/logout/');

        $response->assertStatus(200);
    }

    /**
     */
    public function test_user_can_request_password_reset_code(): void
    {
        \Artisan::call('db:seed ConfigurationSeeder');
        $user = User::factory()->create();

        $response = $this->withCredentials()
            ->post(
                '/api/auth/forgot-password/',
                [
                    'email' => $user->email,
                ],
                [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'accept' => 'application/json',
                ]
            );

        $response->assertStatus(201);
    }

    /**
     */
    public function test_user_can_confirm_password_reset_code(): void
    {
        \Artisan::call('db:seed ConfigurationSeeder');
        $user = User::factory()->create();

        $this->withCredentials()
            ->post(
                '/api/auth/forgot-password/',
                [
                    'email' => $user->email,
                ],
                [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'accept' => 'application/json',
                ]
            );

        $c = PasswordCodeResets::whereEmail($user->email)->first('code');

        $response = $this->withCredentials()
            ->post(
                '/api/auth/reset-password/check-code/',
                [
                    'code' => $c->code,
                ],
                [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'accept' => 'application/json',
                ]
            );

        $response->assertStatus(202);
    }

    /**
     */
    public function test_user_can_reset_password(): void
    {
        \Artisan::call('db:seed ConfigurationSeeder');
        $user = User::factory()->create();

        $this->withCredentials()
            ->post(
                '/api/auth/forgot-password/',
                [
                    'email' => $user->email,
                ],
                [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'accept' => 'application/json',
                ]
            );

        $c = PasswordCodeResets::whereEmail($user->email)->first('code');

        $response = $this->withCredentials()
            ->post(
                '/api/auth/reset-password/',
                [
                    'code' => $c->code,
                    'password' => 'password1',
                    'password_confirmation' => 'password1',
                ],
                [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'accept' => 'application/json',
                ]
            );

        $response->assertStatus(202);
    }
}
