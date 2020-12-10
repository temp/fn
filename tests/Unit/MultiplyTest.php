<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\multiply;

/**
 * @covers \Fnc\multiply
 */
final class MultiplyTest extends TestCase
{
    public function testDivide(): void
    {
        $result = multiply(12, 3);

        $this->assertSame(36, $result);
    }

    public function testZeroA(): void
    {
        $result = multiply(0, 3);

        $this->assertSame(0, $result);
    }

    public function testNullA(): void
    {
        $result = multiply(null, 3);

        $this->assertSame(0, $result);
    }

    public function testZeroB(): void
    {
        $result = multiply(12, 0);

        $this->assertSame(0, $result);
    }

    public function testNullB(): void
    {
        $result = multiply(12, null);

        $this->assertSame(0, $result);
    }

    public function testCurriedDivide(): void
    {
        $identity = multiply(12);

        $result = $identity(3);

        $this->assertSame(36, $result);
    }
}
