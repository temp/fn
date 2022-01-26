<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\contains;

/**
 * @covers \Fnc\contains
 */
final class ContainsTest extends TestCase
{
    public function testArrayValue(): void
    {
        $data = [1, 99, 30];

        $result = contains(99, $data);

        $this->assertTrue($result);
    }

    public function testCurriedArrayValue(): void
    {
        $data = [1, 99, 30];

        $contains = contains(99);

        $result = $contains($data);

        $this->assertTrue($result);
    }
}
