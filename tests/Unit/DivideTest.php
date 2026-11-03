<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\divide;

use const INF;

#[CoversFunction('Fnc\divide')]
final class DivideTest extends TestCase
{
    public function testDivide(): void
    {
        $result = divide(12, 3);

        $this->assertSame(4, $result);
    }

    public function testZeroA(): void
    {
        $result = divide(0, 3);

        $this->assertSame(0, $result);
    }

    public function testNullA(): void
    {
        $result = divide(null, 3);

        $this->assertSame(0, $result);
    }

    public function testZeroB(): void
    {
        $result = divide(12, 0);

        $this->assertSame(INF, $result);
    }

    public function testNullB(): void
    {
        $result = divide(12, null);

        $this->assertSame(INF, $result);
    }

    public function testCurriedDivide(): void
    {
        $identity = divide(12);

        $result = $identity(3);

        $this->assertSame(4, $result);
    }

    public function testNegativeByZero(): void
    {
        $this->assertSame(-INF, divide(-12, 0));
        $this->assertSame(-INF, divide(12, -0.0));
    }

    public function testZeroByZero(): void
    {
        $this->assertNan(divide(0, 0));
    }

    public function testFloat(): void
    {
        $this->assertSame(2.5, divide(5, 2));
    }

    public function testFalsyAndStringZeroDivisors(): void
    {
        $this->assertSame(INF, divide(6, '0'));
        $this->assertSame(INF, divide(6, false));
        $this->assertSame(INF, divide(6, ''));
        $this->assertSame(INF, divide('6', 0));
        $this->assertSame(3, divide('6', '2'));
    }
}
