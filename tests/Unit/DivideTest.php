<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\divide;

/**
 * @covers \Fnc\divide
 */
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

        $this->assertNull($result);
    }

    public function testZeroB(): void
    {
        $result = divide(12, 0);

        $this->assertNull($result);
    }

    public function testNullB(): void
    {
        $result = divide(12, null);

        $this->assertNull($result);
    }

    public function testCurriedDivide(): void
    {
        $identity = divide(12);

        $result = $identity(3);

        $this->assertSame(4, $result);
    }
}
