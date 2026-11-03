<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\add;

#[CoversFunction('Fnc\add')]
final class AddTest extends TestCase
{
    public function testAdd(): void
    {
        $this->assertSame(5, add(2, 3));
        $this->assertSame(5.5, add(2.5, 3));
    }

    public function testNullCountsAsZero(): void
    {
        $this->assertSame(3, add(null, 3));
        $this->assertSame(2, add(2, null));
    }

    public function testCurried(): void
    {
        $this->assertSame(5, add(2)(3));
    }
}
