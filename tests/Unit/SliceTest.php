<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\slice;

#[CoversFunction('Fnc\slice')]
final class SliceTest extends TestCase
{
    public function testString(): void
    {
        $this->assertSame('08:15:00', slice(11, 19, '1753-01-01 08:15:00.000'));
        $this->assertSame('cd', slice(-2, null, 'abcd'));
        $this->assertSame('bc', slice(1, -1, 'abcd'));
        $this->assertSame('', slice(3, 1, 'abcd'));
        $this->assertSame('', slice(10, 20, 'abcd'));
    }

    public function testList(): void
    {
        $this->assertSame([2, 3], slice(1, 3, [1, 2, 3, 4]));
        $this->assertSame([3, 4], slice(-2, null, [1, 2, 3, 4]));
        $this->assertSame([], slice(2, 2, [1, 2, 3]));
        $this->assertSame(['b' => 2], slice(1, 2, ['a' => 1, 'b' => 2, 'c' => 3]));
    }

    public function testCurried(): void
    {
        $time = slice(11, 19);

        $this->assertSame('08:15:00', $time('1753-01-01 08:15:00.000'));
    }
}
