<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\head;

/** @covers \Fnc\head */
final class HeadTest extends TestCase
{
    public function testHead(): void
    {
        $this->assertEquals(1, head([1, 2, 3]));
        $this->assertEquals(1, head(['a' => 1, 'b' => 2, 'c' => 3]));
        $this->assertEquals(null, head([]));
    }
}
