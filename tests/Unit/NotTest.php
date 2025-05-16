<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\not;

#[CoversFunction('Fnc\not')]
final class NotTest extends TestCase
{
    public function testNot(): void
    {
        $this->assertFalse(not(true));
        $this->assertTrue(not(false));
    }
}
