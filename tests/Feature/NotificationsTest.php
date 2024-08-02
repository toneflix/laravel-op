<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SendCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testCanSendVerificationCode(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo(
            $user,
            function (SendCode $notification, array $channels) use ($user) {
                return $notification->code === $user->email_verify_code;
            }
        );
    }
}
