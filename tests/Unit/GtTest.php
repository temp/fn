<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\gt;

#[CoversFunction('Fnc\gt')]
final class GtTest extends TestCase
{
    public function testCompare(): void
    {
        $this->assertTrue(gt(2, 1));
        $this->assertFalse(gt(1, 1));
        $this->assertFalse(gt(1, 2));
    }

    public function testCurriedFirstArgumentIsLeftOperand(): void
    {
        $this->assertTrue(gt(2)(1));
    }
}
