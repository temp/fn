<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\map;
use function Fnc\tail;

#[CoversFunction('Fnc\tail')]
final class TailTest extends TestCase
{
    public function testTail(): void
    {
        $this->assertEquals([2, 3], tail([1, 2, 3]));

        $this->assertEquals([], tail([]));

        $stack = ['orange', 'banana', 'apple', 'raspberry'];
        $this->assertEquals(['banana', 'apple', 'raspberry'], tail($stack));

        $stack = ['a' => '1', 'b' => 2, 'c' => 3];
        $this->assertEquals(['b' => 2, 'c' => 3], tail($stack));
    }

    public function testString(): void
    {
        $this->assertSame('bc', tail('abc'));
        $this->assertSame('', tail('a'));
        $this->assertSame('', tail(''));
    }

    public function testCurried(): void
    {
        $this->assertSame([[2], []], map(tail(), [[1, 2], [4]]));
    }
}
