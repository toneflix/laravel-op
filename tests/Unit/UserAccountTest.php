<?php

namespace Tests\Unit;

use App\Enums\HttpStatus;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAccountTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_can_request_account_deletion(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response = $this->delete('/api/account/delete', [
            'reason' => fake('En-NG')->sentence,
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertAccepted();
        $this->assertNotNull($user->deleting_at);
    }

    public function test_deleted_account_cant_login(): void
    {
        $user = User::factory()->create(['deleting_at' => now()]);

        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'accept' => 'application/json',
        ]);

        $response->assertUnprocessable();
        $this->assertNotNull($user->deleting_at);
    }
}
