<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\tail;

/**
 * @covers \Fnc\tail
 */
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
}
