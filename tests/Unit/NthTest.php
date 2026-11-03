<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\nth;

#[CoversFunction('Fnc\nth')]
final class NthTest extends TestCase
{
    public function testList(): void
    {
        $this->assertSame('b', nth(1, ['a', 'b', 'c']));
        $this->assertSame('c', nth(-1, ['a', 'b', 'c']));
        $this->assertSame(2, nth(1, ['x' => 1, 'y' => 2]));
    }

    public function testOutOfRange(): void
    {
        $this->assertNull(nth(3, ['a']));
        $this->assertNull(nth(-2, ['a']));
    }

    public function testString(): void
    {
        $this->assertSame('b', nth(1, 'abc'));
        $this->assertSame('c', nth(-1, 'abc'));
        $this->assertSame('', nth(5, 'abc'));
    }
}
