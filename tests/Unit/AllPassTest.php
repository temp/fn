<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\allPass;

#[CoversFunction('Fnc\allPass')]
final class AllPassTest extends TestCase
{
    public function testAllPass(): void
    {
        $isPositiveEven = allPass([static fn ($n) => $n > 0, static fn ($n) => $n % 2 === 0]);

        $this->assertTrue($isPositiveEven(4));
        $this->assertFalse($isPositiveEven(3));
        $this->assertFalse($isPositiveEven(-2));
    }

    public function testEmptyListIsSatisfied(): void
    {
        $this->assertTrue(allPass([])(1));
    }

    public function testStopsAtFirstFailure(): void
    {
        $called = false;
        $fn = allPass([
            static fn () => false,
            static function () use (&$called) {
                $called = true;

                return true;
            },
        ]);

        $this->assertFalse($fn(1));
        $this->assertFalse($called);
    }
}
