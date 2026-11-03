<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\head;
use function Fnc\map;
use function Fnc\pipe;
use function Fnc\reverse;

#[CoversFunction('Fnc\head')]
final class HeadTest extends TestCase
{
    public function testHead(): void
    {
        $this->assertEquals(1, head([1, 2, 3]));
        $this->assertEquals(1, head(['a' => 1, 'b' => 2, 'c' => 3]));
        $this->assertEquals(null, head([]));
    }

    public function testString(): void
    {
        $this->assertSame('a', head('abc'));
        $this->assertSame('', head(''));
    }

    public function testNullFirstElement(): void
    {
        $this->assertNull(head([null, 1]));
    }

    public function testCurried(): void
    {
        $this->assertSame(1, pipe(reverse(), head())([3, 2, 1]));
        $this->assertSame([1, 4], map(head(), [[1, 2], [4]]));
    }
}
