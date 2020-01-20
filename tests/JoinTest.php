<?php

declare(strict_types=1);

namespace FncTests;

use PHPUnit\Framework\TestCase;
use function Fnc\join;

/**
 * @covers \Fnc\Join
 */
final class JoinTest extends TestCase
{
    public function testWithZeroElements(): void
    {
        $join = join('_');

        $result = $join();

        $this->assertSame('', $result);
    }

    public function testWithOneElement(): void
    {
        $join = join('_');

        $result = $join('foo');

        $this->assertSame('foo', $result);
    }

    public function testWithTwoElements(): void
    {
        $pick = join('_');

        $result = $pick('abc', 'def');

        $this->assertSame('abc_def', $result);
    }
}
