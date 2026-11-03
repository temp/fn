<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\gte;

#[CoversFunction('Fnc\gte')]
final class GteTest extends TestCase
{
    public function testCompare(): void
    {
        $this->assertTrue(gte(2, 1));
        $this->assertTrue(gte(1, 1));
        $this->assertFalse(gte(1, 2));
    }

    public function testCurriedFirstArgumentIsLeftOperand(): void
    {
        $this->assertTrue(gte(2)(1));
    }
}
