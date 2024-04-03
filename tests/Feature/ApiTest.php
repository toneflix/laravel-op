<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testGetUser(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->get('/api/user');
        $response->assertOk();
    }

    /**
     * A basic feature test example.
     */
    public function testRequestsCanBeRateLimited(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $response = $this->get('/api/');
        }
        $response->assertTooManyRequests();
    }
}
