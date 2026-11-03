<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\isNil;

#[CoversFunction('Fnc\isNil')]
final class IsNilTest extends TestCase
{
    public function testIsNil(): void
    {
        $this->assertTrue(isNil(null));
        $this->assertFalse(isNil(0));
        $this->assertFalse(isNil(''));
        $this->assertFalse(isNil(false));
    }
}
