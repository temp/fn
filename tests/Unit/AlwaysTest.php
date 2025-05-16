<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\always;

#[CoversFunction('Fnc\always')]
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
