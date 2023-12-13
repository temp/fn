<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\always;

/** @covers \Fnc\always */
final class AlwaysTest extends TestCase
{
    public function testStringValue(): void
    {
        $always = always('foo');

        $this->assertSame('foo', $always());
    }

    public function testArrayValue(): void
    {
        $always = always([1, 99, 30]);

        $this->assertSame([1, 99, 30], $always());
    }
}
