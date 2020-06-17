<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use stdClass;
use function Fnc\emptyVal;

/**
 * @covers \Fnc\emptyVal
 */
final class EmptyValTest extends TestCase
{
    public function testString(): void
    {
        $result = emptyVal('foo');

        $this->assertSame('', $result);
    }

    public function testEmptyString(): void
    {
        $result = emptyVal('');

        $this->assertSame('', $result);
    }

    public function testInt(): void
    {
        $result = emptyVal(1);

        $this->assertSame(0, $result);
    }

    public function testZeroInt(): void
    {
        $result = emptyVal(0);

        $this->assertSame(0, $result);
    }

    public function testFloat(): void
    {
        $result = emptyVal(1.0);

        $this->assertSame(0.0, $result);
    }

    public function testZeroFkiat(): void
    {
        $result = emptyVal(0.0);

        $this->assertSame(0.0, $result);
    }

    public function testArray(): void
    {
        $result = emptyVal([1, 2]);

        $this->assertSame([], $result);
    }

    public function testEmptyArray(): void
    {
        $result = emptyVal([]);

        $this->assertSame([], $result);
    }

    public function testNull(): void
    {
        $result = emptyVal(null);

        $this->assertNull($result);
    }

    public function testObject(): void
    {
        $result = emptyVal(new stdClass());

        $this->assertEquals(new stdClass(), $result);
    }
}
