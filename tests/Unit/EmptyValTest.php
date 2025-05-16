<?php

declare(strict_types=1);

namespace FncTests\Unit;

use Closure;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;
use stdClass;

use function Fnc\emptyVal;

#[CoversFunction('Fnc\emptyVal')]
final class EmptyValTest extends TestCase
{
    public function testBool(): void
    {
        $result = emptyVal(true);

        $this->assertFalse($result);
    }

    public function testEmptyBool(): void
    {
        $result = emptyVal(false);

        $this->assertFalse($result);
    }

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

    public function testZeroFloat(): void
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

    public function testFunction(): void
    {
        $result = emptyVal(static fn ($x) => $x);

        $this->assertInstanceOf(Closure::class, $result);
    }
}
