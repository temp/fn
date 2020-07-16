<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;
use function Fnc\sum;

// phpcs:disable Generic.PHP.ForbiddenFunctions.FoundWithAlternative

/**
 * @covers \Fnc\sum
 */
final class SumTest extends TestCase
{
    public function testSum(): void
    {
        $data = [1, 2];

        $result = sum($data);

        $this->assertSame(3, $result);
    }

    public function testWithNull(): void
    {
        $data = [1, 2, null];

        $result = sum($data);

        $this->assertSame(3, $result);
    }
}
