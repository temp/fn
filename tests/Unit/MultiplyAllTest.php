<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\multiplyAll;

/**
 * @covers \Fnc\multiplyAll
 */
final class MultiplyAllTest extends TestCase
{
    public function testMultiply(): void
    {
        $result = multiplyAll([6, 3, 2]);

        $this->assertSame(36, $result);
    }

    public function testZero(): void
    {
        $result = multiplyAll([6, 3, 0]);

        $this->assertSame(0, $result);
    }

    public function testNull(): void
    {
        $result = multiplyAll([6, 3, null]);

        $this->assertSame(0, $result);
    }

    public function testEmpty(): void
    {
        $result = multiplyAll([]);

        $this->assertSame(0, $result);
    }

    public function testNotAnArray(): void
    {
        $result = multiplyAll('not an array');

        $this->assertSame(0, $result);
    }

    public function testCurriedMultiply(): void
    {
        $identity = multiplyAll();

        $result = $identity([6, 3, 2]);

        $this->assertSame(36, $result);
    }
}
