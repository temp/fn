<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\both;

#[CoversFunction('Fnc\both')]
final class BothTest extends TestCase
{
    public function testBoth(): void
    {
        $fn = both(static fn ($n) => $n > 0, static fn ($n) => $n < 10);

        $this->assertTrue($fn(5));
        $this->assertFalse($fn(0));
        $this->assertFalse($fn(10));
    }

    public function testCurried(): void
    {
        $this->assertTrue(both(static fn () => true)(static fn () => true)());
    }
}
