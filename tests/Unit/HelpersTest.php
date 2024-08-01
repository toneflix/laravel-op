<?php

namespace Tests\Unit;

use App\Helpers\Strings;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    use InteractsWithConsole;

    public function testCanValidateJson(): void
    {
        $this->assertTrue(Strings::jsonValidate('{"name": "Doe"}'));
    }

    public function testConfigurationCommand(): void
    {
        $this->artisan("app:set-config")
            ->expectsQuestion('What do you want to configure?', 'app_name')
            ->expectsQuestion('What do you want to set as the value for app_name?', 'Test Site')
            ->assertSuccessful();
    }
}
