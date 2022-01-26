<?php

declare(strict_types=1);

namespace FncTests\Unit;

use PHPUnit\Framework\TestCase;

use function Fnc\merge;

/**
 * @covers \Fnc\merge
 */
final class MergeTest extends TestCase
{
    public function testMerge(): void
    {
        $data = ['color' => 'red', 2, 4];
        $b = ['a', 'b', 'color' => 'green', 'shape' => 'trapezoid', 4];
        $result = merge($data, $b);

        $this->assertEquals(['color' => 'green', 2, 4, 'a', 'b', 'shape' => 'trapezoid', 4], $result);
    }

    public function testCurriedMerge(): void
    {
        $data = ['color' => 'red', 2, 4];
        $b = ['a', 'b', 'color' => 'green', 'shape' => 'trapezoid', 4];

        $merge = merge($data);

        $result = $merge($b);

        $this->assertEquals(['color' => 'green', 2, 4, 'a', 'b', 'shape' => 'trapezoid', 4], $result);
    }

    public function testMergeEmpty(): void
    {
        $this->assertEquals([1, 2], merge([1, 2], []));
    }
}
