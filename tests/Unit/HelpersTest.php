<?php

namespace Tests\Unit;

use App\Helpers\Strings;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function testJsonValidator(): void
    {
        $this->assertTrue(Strings::jsonValidate('{"name": "Doe"}'));
    }
}
