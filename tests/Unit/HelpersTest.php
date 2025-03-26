<?php

namespace Tests\Unit;

use App\Helpers\Provider;
use App\Helpers\Strings;
use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    use InteractsWithConsole;

    public function testCanValidateJson(): void
    {
        $this->assertTrue(Strings::jsonValidate('{"name": "Doe"}'));
    }

    public function testMessageParserWorks(): void
    {
        $user = User::factory()->create();

        $message = Provider::messageParser(
            'send_code::verify',
            $user,
            [
                'type' => 'email',
                'code' => '111111',
                'token' => md5(time()),
                'label' => 'email address',
                'app_url' => config('app.frontend_url', config('app.url')),
                'app_name' => Provider::config('app_name'),
                'duration' => '10 seconds',
            ]
        );

        $this->assertCount(count($message->lines) - 1, config('messages.send_code::verify')['lines']);
    }
}
