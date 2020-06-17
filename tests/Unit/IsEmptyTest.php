<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use stdClass;
use function Fnc\isEmpty;

/**
 * @covers \Fnc\isEmpty
 */
final class IsEmptyTest extends TestCase
{
    public function testString(): void
    {
        $result = isEmpty('foo');

        $this->assertFalse($result);
    }

    public function testEmptyString(): void
    {
        $result = isEmpty('');

        $this->assertTrue($result);
    }

    public function testInt(): void
    {
        $result = isEmpty(1);

        $this->assertFalse($result);
    }

    public function testZeroInt(): void
    {
        $result = isEmpty(0);

        $this->assertTrue($result);
    }

    public function testArray(): void
    {
        $result = isEmpty([1, 2]);

        $this->assertFalse($result);
    }

    public function testEmptyArray(): void
    {
        $result = isEmpty([]);

        $this->assertTrue($result);
    }

    public function testNull(): void
    {
        $result = isEmpty(null);

        $this->assertTrue($result);
    }

    public function testObject(): void
    {
        $result = isEmpty(new stdClass());

        $this->assertFalse($result);
    }
}
