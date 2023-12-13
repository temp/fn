<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\_pipe;
use function round;

/** @covers \Fnc\_pipe */
final class _PipeTest extends TestCase
{
    public function testNumericArrayValue(): void
    {
        $multiply = static fn ($x, $y) => $x * $y;
        $round = static fn ($z) => round($z);

        $pipe = _pipe($multiply, $round);

        $this->assertEquals(9, $pipe(2.5, 3.7));
    }
}
