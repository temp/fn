<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function Fnc\includes;

#[CoversFunction('Fnc\includes')]
final class IncludesTest extends TestCase
{
    public function testList(): void
    {
        $this->assertTrue(includes(2, [1, 2, 3]));
        $this->assertFalse(includes('2', [1, 2, 3]));
    }

    public function testUsesEquals(): void
    {
        $this->assertTrue(includes(['a' => 1], [['a' => 1], ['b' => 2]]));
    }

    public function testString(): void
    {
        $this->assertTrue(includes('ell', 'hello'));
        $this->assertFalse(includes('x', 'hello'));
        $this->assertFalse(includes(1, '1'));
    }
}
