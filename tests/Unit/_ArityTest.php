<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\_arity;

// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps

/**
 * @covers \Fnc\_arity
 */
final class _ArityTest extends TestCase
{
    public function testNumericArrayValue(): void
    {
        $ar1 = _arity(1, static fn ($x) => $x);
        $ar2 = _arity(2, static fn ($x, $y) => $x * $y);
        $ar3 = _arity(3, static fn ($x, $y, $z) => $x * $y * $z);

        $this->assertSame(1, $ar1(1));
        $this->assertSame(6, $ar2(2, 3));
        $this->assertSame(20, $ar2(4, 5));
        $this->assertSame(336, $ar3(6, 7, 8));
    }
}
