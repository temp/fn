<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\always;
use function Fnc\cond;

#[CoversFunction('Fnc\cond')]
final class CondTest extends TestCase
{
    public function testFirstMatchingPairWins(): void
    {
        $fn = cond([
            [static fn ($n) => $n < 0, always('negative')],
            [static fn ($n) => $n === 0, always('zero')],
            [always(true), static fn ($n) => 'positive ' . $n],
        ]);

        $this->assertSame('negative', $fn(-1));
        $this->assertSame('zero', $fn(0));
        $this->assertSame('positive 5', $fn(5));
    }

    public function testNoMatchReturnsNull(): void
    {
        $this->assertNull(cond([[always(false), always('x')]])(1));
    }
}
