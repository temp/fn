<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\lte;

#[CoversFunction('Fnc\lte')]
final class LteTest extends TestCase
{
    public function testCompare(): void
    {
        $this->assertTrue(lte(1, 2));
        $this->assertTrue(lte(1, 1));
        $this->assertFalse(lte(2, 1));
    }

    public function testCurriedFirstArgumentIsLeftOperand(): void
    {
        $this->assertTrue(lte(1)(2));
    }
}
